<?php

namespace App\Modules\AiPublisher\Models;

use App\Modules\Shared\Models\Concerns\HasUuid;
use App\Modules\Tenant\Models\Concerns\BelongsToTenant;
use App\Modules\Post\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiContentJob extends Model
{
    use BelongsToTenant;
    use HasUuid;

    protected $fillable = [
        'type', 'requested_by', 'source', 'topic',
        'status', 'payload', 'result', 'error',
        'created_post_id', 'started_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return ['payload' => 'array', 'result' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime'];
    }

    public function createdPost(): BelongsTo { return $this->belongsTo(Post::class, 'created_post_id'); }

    public function markStarted(): void { $this->forceFill(['status' => 'processing', 'started_at' => now()])->save(); }

    public function markCompleted(int $postId, array $result = []): void
    {
        $this->forceFill(['status' => 'completed', 'created_post_id' => $postId, 'result' => $result, 'finished_at' => now()])->save();
    }

    public function markFailed(string $error): void { $this->forceFill(['status' => 'failed', 'error' => $error, 'finished_at' => now()])->save(); }
}
