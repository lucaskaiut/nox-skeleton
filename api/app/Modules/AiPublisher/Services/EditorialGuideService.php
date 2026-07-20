<?php

namespace App\Modules\AiPublisher\Services;

class EditorialGuideService
{
    /**
     * @return array{company: string, audience: list<string>, tone: string, content_rules: list<string>}
     */
    public function get(): array
    {
        return [
            'company' => config('ai-publisher.editorial.company', ''),
            'audience' => config('ai-publisher.editorial.audience', []),
            'tone' => config('ai-publisher.editorial.tone', 'professional'),
            'content_rules' => config('ai-publisher.editorial.content_rules', []),
        ];
    }
}
