<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Application Open',
                'subject' => 'Scholarship Applications are Now Open',
                'body' => 'We are pleased to inform you that scholarship applications for the current semester are now open. Log in to SmartMatch to view your matched scholarships and submit your application.',
                'channel' => 'both',
            ],
            [
                'name' => 'Deadline Reminder',
                'subject' => 'Reminder: Scholarship Application Deadline is Approaching',
                'body' => 'This is a reminder that the scholarship application deadline is approaching. Please log in to SmartMatch and ensure your application and required documents are submitted before the deadline.',
                'channel' => 'both',
            ],
            [
                'name' => 'Application Approved',
                'subject' => 'Congratulations! Your Scholarship Application has been Approved',
                'body' => 'We are pleased to inform you that your scholarship application has been reviewed and approved. Please log in to SmartMatch for further instructions regarding your scholarship.',
                'channel' => 'both',
            ],
            [
                'name' => 'Documents Required',
                'subject' => 'Action Required: Please Submit Your Documents',
                'body' => 'Your scholarship application is currently pending document submission. Please log in to SmartMatch and upload the required documents to proceed with your application.',
                'channel' => 'both',
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::firstOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
