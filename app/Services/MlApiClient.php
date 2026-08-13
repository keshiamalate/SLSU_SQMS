<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MlApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ml_api.url', 'http://127.0.0.1:5000');
    }

    /**
     * Check if the ML API is reachable and model is loaded.
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->baseUrl}/health");
            return $response->ok() && $response->json('status') === 'ok';
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Get a single probability score for one student-scholarship pair.
     */
    public function predict(array $features): float
    {
        try {
            $response = Http::timeout(5)
                ->post("{$this->baseUrl}/predict", $features);

            if ($response->ok()) {
                return (float) $response->json('probability', 0.5);
            }

            Log::warning('ML API predict failed: ' . $response->body());
            return 0.5; // neutral fallback

        } catch (\Throwable $e) {
            Log::error('ML API unreachable: ' . $e->getMessage());
            return 0.5;
        }
    }

    /**
     * Get probability scores for multiple scholarships at once.
     * Returns array keyed by scholarship_id => probability
     */
    public function predictBatch(array $records): array
    {
        try {
            $response = Http::timeout(10)
                ->post("{$this->baseUrl}/predict/batch", ['records' => $records]);

            if ($response->ok()) {
                $results = [];
                foreach ($response->json('results', []) as $result) {
                    if (isset($result['scholarship_id']) && !isset($result['error'])) {
                        $results[$result['scholarship_id']] = (float) $result['probability'];
                    }
                }
                return $results;
            }

        } catch (\Throwable $e) {
            Log::error('ML API batch predict failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get model metadata from the API.
     */
    public function getModelInfo(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/model/info");
            return $response->ok() ? $response->json() : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
