<?php

namespace App\Modules\AiPublisher\Services;

class DiscoveryGenerator
{
    /**
     * @return array{name: string, version: string, authentication: string, resources: array<string, bool>, endpoints: array<string, string>}
     */
    public function generate(): array
    {
        return [
            'name' => config('app.name', 'CMS'),
            'version' => '1.0',
            'authentication' => 'Bearer Token (Sanctum personal access token or API token)',
            'resources' => [
                'posts' => true,
                'categories' => true,
                'tags' => true,
            ],
            'endpoints' => [
                'documentation' => url('/api/ai/docs'),
                'schema_post' => url('/api/ai/schema/post'),
                'schema_category' => url('/api/ai/schema/category'),
                'editorial_guide' => url('/api/ai/editorial-guide'),
                'publish' => url('/api/ai/posts'),
            ],
        ];
    }

    /**
     * @return array{recommended_flow: list<string>, tips: list<string>}
     */
    public function documentation(): array
    {
        return [
            'recommended_flow' => [
                '1. Call GET /api/ai/discovery to see available resources.',
                '2. Call GET /api/ai/schema/post to understand required/optional fields.',
                '3. Call GET /api/ai/editorial-guide for tone and content rules.',
                '4. Call GET /api/categories to list existing categories.',
                '5. Create a category via POST /api/categories if needed.',
                '6. Generate content following the editorial guide.',
                '7. Call POST /api/ai/posts to submit as draft.',
                '8. Posts are always created as draft — they will NOT be published automatically.',
                '9. Always generate a slug from the title.',
                '10. Always generate an excerpt (or let the system auto-generate it).',
                '11. Always generate SEO metadata (meta_title and meta_description), or let the system auto-generate them.',
            ],
            'tips' => [
                'Use H2 and H3 headings for structure.',
                'Generate a compelling meta_description (max 320 chars).',
                'Content must be at least '.config('ai-publisher.min_content_length', 200).' characters.',
                'The excerpt is auto-generated from the first 400 chars if not provided.',
                'Reading time is automatically estimated.',
                'All AI-generated content is audited in the ai_content_jobs table.',
            ],
        ];
    }
}
