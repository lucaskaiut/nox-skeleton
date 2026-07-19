<?php

namespace App\Modules\Tenant\Resolution\Strategies;

use App\Modules\Tenant\Models\Tenant;
use App\Modules\Tenant\Resolution\Contracts\ResolutionStrategy;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;

class AuthenticatedUserStrategy implements ResolutionStrategy
{
    public function resolve(Request $request): ?Tenant
    {
        $user = $request->user() ?? $request->user('sanctum');

        if (! $user instanceof User) {
            return null;
        }

        return $user->tenant;
    }
}
