<?php

namespace App\Modules\Tenant\Models;

use App\Modules\ACL\Models\Role;
use App\Modules\ApiToken\Models\ApiToken;
use App\Modules\Shared\Models\Concerns\HasUuid;
use App\Modules\User\Models\User;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    use HasUuid;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'domain',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    protected static function newFactory(): TenantFactory
    {
        return TenantFactory::new();
    }
}
