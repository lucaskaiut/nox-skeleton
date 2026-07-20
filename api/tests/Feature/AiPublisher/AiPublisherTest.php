<?php

namespace Tests\Feature\AiPublisher;

use App\Modules\AiPublisher\Models\AiContentJob;
use App\Modules\Post\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\InteractsWithTenants;
use Tests\TestCase;

class AiPublisherTest extends TestCase
{
    use InteractsWithTenants;
    use RefreshDatabase;

    public function test_discovery_returns_resources_and_endpoints(): void
    {
        $tenant = $this->createTenantWithRoles();
        Sanctum::actingAs($this->createAdmin($tenant));

        $this->getJson('/api/ai/discovery')
            ->assertOk()
            ->assertJsonPath('data.resources.posts', true)
            ->assertJsonPath('data.resources.categories', true)
            ->assertJsonPath('data.endpoints.publish', route('ai.publish'));
    }

    public function test_docs_returns_recommended_flow(): void
    {
        $tenant = $this->createTenantWithRoles();
        Sanctum::actingAs($this->createAdmin($tenant));

        $this->getJson('/api/ai/docs')
            ->assertOk()
            ->assertJsonPath('data.recommended_flow.0', fn (string $val) => str_contains($val, 'discovery'));
    }

    public function test_schema_post_is_dynamic(): void
    {
        $tenant = $this->createTenantWithRoles();
        Sanctum::actingAs($this->createAdmin($tenant));

        $this->getJson('/api/ai/schema/post')
            ->assertOk()
            ->assertJsonPath('data.resource', 'post')
            ->assertJsonPath('data.required', ['title', 'status'])
            ->assertJsonPath('data.statuses', ['draft', 'review', 'published']);
    }

    public function test_editorial_guide_returns_configured_values(): void
    {
        $tenant = $this->createTenantWithRoles();
        Sanctum::actingAs($this->createAdmin($tenant));

        $this->getJson('/api/ai/editorial-guide')
            ->assertOk()
            ->assertJsonPath('data.tone', 'profissional');
    }

    public function test_ai_publish_creates_post_as_draft_and_audits_job(): void
    {
        $tenant = $this->createTenantWithRoles();
        Sanctum::actingAs($this->createAdmin($tenant));

        Category::factory()->for($tenant)->create(['name' => 'Notícias', 'slug' => 'noticias']);

        $response = $this->postJson('/api/ai/posts', [
            'title' => 'Post gerado por IA',
            'content' => '<p>'.str_repeat('Conteúdo do artigo gerado por inteligência artificial. ', 10).'</p>',
            'category' => 'Notícias',
            'tags' => ['IA', 'Automação'],
            'source' => 'hermes',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.slug', 'post-gerado-por-ia')
            ->assertJsonPath('data.tags.0.name', 'IA');

        $postUuid = $response->json('data.id');

        $this->assertDatabaseHas('posts', [
            'uuid' => $postUuid,
            'status' => 'draft',
            'reading_time' => 1,
        ]);

        $this->assertDatabaseHas('ai_content_jobs', [
            'source' => 'hermes',
            'status' => 'completed',
            'type' => 'post',
            'topic' => 'Post gerado por IA',
        ]);

        $this->assertNotNull(AiContentJob::query()->first()->created_post_id);
    }

    public function test_ai_publish_auto_generates_missing_fields(): void
    {
        $tenant = $this->createTenantWithRoles();
        Sanctum::actingAs($this->createAdmin($tenant));

        $response = $this->postJson('/api/ai/posts', [
            'title' => 'Título do Post Automático',
            'content' => '<p>'.str_repeat('Texto com conteúdo suficiente para validação de tamanho mínimo. ', 5).'</p>',
        ])
            ->assertCreated()
            ->assertJsonPath('data.excerpt', fn (string $v) => ! blank($v))
            ->assertJsonPath('data.meta_title', fn (string $v) => ! blank($v))
            ->assertJsonPath('data.meta_description', fn (string $v) => ! blank($v))
            ->assertJsonPath('data.slug', 'titulo-do-post-automatico')
            ->assertJsonPath('data.reading_time', fn (int $v) => $v > 0);
    }

    public function test_ai_publish_fails_on_short_content(): void
    {
        $tenant = $this->createTenantWithRoles();
        Sanctum::actingAs($this->createAdmin($tenant));

        $this->postJson('/api/ai/posts', [
            'title' => 'Curto',
            'content' => 'Muito curto.',
        ])->assertStatus(500);
    }

    public function test_member_without_ai_permission_cannot_access(): void
    {
        $tenant = $this->createTenantWithRoles();
        Sanctum::actingAs($this->createMember($tenant));

        $this->getJson('/api/ai/discovery')->assertForbidden();
        $this->postJson('/api/ai/posts', ['title' => 'X', 'content' => str_repeat('A', 300)])->assertForbidden();
    }
}
