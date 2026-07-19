<?php

namespace App\Modules\ApiToken\Models;

use App\Modules\Tenant\Models\Concerns\BelongsToTenant;
use Database\Factories\ApiTokenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    public const PREFIX = 'api_';

    use BelongsToTenant;

    /** @use HasFactory<ApiTokenFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'token_hash',
        'last_used_at',
        'expires_at',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public static function hash(string $plainTextToken): string
    {
        return hash('sha256', $plainTextToken);
    }

    protected static function newFactory(): ApiTokenFactory
    {
        return ApiTokenFactory::new();
    }
}
