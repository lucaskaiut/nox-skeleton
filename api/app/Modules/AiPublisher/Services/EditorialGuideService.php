<?php

namespace App\Modules\AiPublisher\Services;

use App\Modules\AiPublisher\Models\AiPublisherSettings;

class EditorialGuideService
{
    public function get(): array
    {
        $settings = AiPublisherSettings::query()->first();

        if ($settings) {
            return [
                'company' => $settings->company ?? config('ai-publisher.editorial.company', ''),
                'audience' => $settings->audience ?? config('ai-publisher.editorial.audience', []),
                'tone' => $settings->tone ?? config('ai-publisher.editorial.tone', 'professional'),
                'content_rules' => $settings->content_rules ?? config('ai-publisher.editorial.content_rules', []),
            ];
        }

        return [
            'company' => config('ai-publisher.editorial.company', ''),
            'audience' => config('ai-publisher.editorial.audience', []),
            'tone' => config('ai-publisher.editorial.tone', 'professional'),
            'content_rules' => config('ai-publisher.editorial.content_rules', []),
        ];
    }
}
