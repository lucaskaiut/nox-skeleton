<?php

namespace Database\Factories;

use App\Modules\Post\Models\Post;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(), 'author_id' => User::factory(),
            'title' => fake()->sentence(), 'slug' => fn (array $a) => Str::slug($a['title']),
            'excerpt' => fake()->sentences(3, true), 'content' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>',
            'reading_time' => fake()->numberBetween(2, 15), 'status' => 'published', 'published_at' => now(),
        ];
    }
}
