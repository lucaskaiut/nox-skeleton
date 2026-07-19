<?php

namespace App\Modules\Tenant\Resolution;

use App\Modules\Tenant\Exceptions\TenantCouldNotBeResolved;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\Tenant\Resolution\Contracts\ResolutionStrategy;
use Illuminate\Http\Request;

class TenantResolver
{
    /**
     * @param  iterable<ResolutionStrategy>  $strategies
     */
    public function __construct(private readonly iterable $strategies) {}

    /**
     * @throws TenantCouldNotBeResolved
     */
    public function resolve(Request $request): Tenant
    {
        foreach ($this->strategies as $strategy) {
            $tenant = $strategy->resolve($request);

            if ($tenant instanceof Tenant) {
                return $tenant;
            }
        }

        throw TenantCouldNotBeResolved::make();
    }
}
