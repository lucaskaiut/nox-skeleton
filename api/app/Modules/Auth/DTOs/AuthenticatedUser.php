<?php

namespace App\Modules\Auth\DTOs;

use App\Modules\Tenant\Models\Tenant;
use App\Modules\User\Models\User;

final readonly class AuthenticatedUser
{
    public function __construct(
        public User $user,
        public Tenant $tenant,
        public ?string $token,
    ) {}
}
