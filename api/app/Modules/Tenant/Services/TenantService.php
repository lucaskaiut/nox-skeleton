<?php

namespace App\Modules\Tenant\Services;

use App\Modules\Tenant\Models\Tenant;

class TenantService
{
    /**
     * @param  array{name: string, document: string, email: string, phone: ?string, domain: string}  $data
     */
    public function create(array $data): Tenant
    {
        return Tenant::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Tenant $tenant, array $data): Tenant
    {
        $tenant->fill($data);
        $tenant->save();

        return $tenant->refresh();
    }
}
