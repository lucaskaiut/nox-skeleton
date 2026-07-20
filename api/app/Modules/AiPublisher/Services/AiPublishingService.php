<?php

namespace App\Modules\AiPublisher\Services;

use App\Modules\AiPublisher\Models\AiContentJob;
use App\Modules\Post\Models\Category;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Models\Tag;
use App\Modules\User\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AiPublishingService
{
    private int $minContentLength;

    private string $defaultStatus;

    public function __construct()
    {
        $this->minContentLength = config('ai-publisher.min_content_length', 200);
        $this->defaultStatus = config('ai-publisher.default_status', 'draft');
    }

    /**
     * @param  array{title: string, content: string, excerpt?: ?string, category?: ?string, tags?: list<string>, meta_title?: ?string, meta_description?: ?string, featured_image?: ?string, source?: ?string, requested_by?: ?string}  $data
     */
    public function publish(array $data): Post
    {
        $this->validate($data);

        /** @var string $excerpt */
        $excerpt = $data['excerpt'] ?? $this->generateExcerpt($data['content']);

        return DB::transaction(function () use ($data, $excerpt): Post {
            $topic = $data['title'];

            $job = AiContentJob::query()->create([
                'type' => 'post',
                'topic' => $topic,
                'requested_by' => $data['requested_by'] ?? null,
                'source' => $data['source'] ?? 'ai',
                'status' => 'processing',
                'payload' => $data,
                'started_at' => now(),
            ]);

            try {
                $post = Post::query()->create([
                    'title' => $data['title'],
                    'slug' => Str::slug($data['title']),
                    'excerpt' => $excerpt,
                    'content' => $data['content'],
                    'status' => $this->defaultStatus,
                    'reading_time' => $this->estimateReadingTime($data['content']),
                    'meta_title' => $data['meta_title'] ?? $this->generateMetaTitle($data['title']),
                    'meta_description' => $data['meta_description'] ?? $this->generateMetaDescription($excerpt),
                    'featured_image' => $data['featured_image'] ?? null,
                    'author_id' => auth()->id() ?? User::query()->first()?->getKey(),
                ]);

                if (isset($data['tags']) && $data['tags'] !== []) {
                    $ids = [];

                    foreach ($data['tags'] as $name) {
                        $tag = \App\Modules\Post\Models\Tag::query()->firstOrCreate(
                            ['name' => $name],
                            ['slug' => Str::slug($name)],
                        );
                        $ids[] = $tag->getKey();
                    }

                    $post->tags()->sync($ids);
                }

                if (isset($data['category'])) {
                    $category = \App\Modules\Post\Models\Category::query()
                        ->where('name', $data['category'])
                        ->orWhere('slug', Str::slug($data['category']))
                        ->first();

                    if ($category) {
                        $post->categories()->sync([$category->getKey()]);
                    }
                }

                $job->markCompleted($post->getKey(), ['title' => $post->title, 'slug' => $post->slug]);

                return $post->load(['author', 'categories', 'tags']);
            } catch (\Throwable $e) {
                $job->markFailed($e->getMessage());

                throw $e;
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validate(array $data): void
    {
        if (blank($data['title'] ?? null)) {
            throw new \InvalidArgumentException('O título é obrigatório.');
        }

        if (blank($data['content'] ?? null)) {
            throw new \InvalidArgumentException('O conteúdo é obrigatório.');
        }

        if (Str::length(strip_tags($data['content'])) < $this->minContentLength) {
            throw new \InvalidArgumentException("O conteúdo deve ter no mínimo {$this->minContentLength} caracteres.");
        }
    }

    private function generateExcerpt(string $content, int $maxLength = 400): string
    {
        $plain = Str::limit(strip_tags($content), $maxLength);

        return rtrim($plain, ' .,;:-');
    }

    private function estimateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));

        return (int) max(1, ceil($wordCount / 200));
    }

    private function generateMetaTitle(string $title): string
    {
        return Str::limit($title, 160, '');
    }

    private function generateMetaDescription(string $excerpt): string
    {
        return Str::limit($excerpt, 320, '');
    }
}
