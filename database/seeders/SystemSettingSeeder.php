<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'consent_version', 'value' => '1.0', 'description' => 'Current consent form version'],
            ['key' => 'min_rf_accuracy', 'value' => '0.85', 'description' => 'Minimum RF accuracy before deployment'],
            ['key' => 'min_rf_f1', 'value' => '0.80', 'description' => 'Minimum RF F1 score before deployment'],
            ['key' => 'session_timeout_minutes', 'value' => '30', 'description' => 'Admin session timeout in minutes'],
            ['key' => 'max_upload_size_mb', 'value' => '5', 'description' => 'Max file upload size in MB'],
            ['key' => 'allowed_upload_types', 'value' => 'pdf,jpg,jpeg,png', 'description' => 'Allowed document types'],
            ['key' => 'notification_queue_driver', 'value' => 'database', 'description' => 'Queue driver for notifications'],
        ];
        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
