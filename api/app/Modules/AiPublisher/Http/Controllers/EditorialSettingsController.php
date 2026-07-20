<?php

namespace App\Modules\AiPublisher\Http\Controllers;

use App\Modules\AiPublisher\Models\AiPublisherSettings;
use App\Modules\AiPublisher\Services\EditorialGuideService;
use App\Modules\Shared\Http\Controllers\ApiController;
use App\Modules\Tenant\Support\Facades\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditorialSettingsController extends ApiController
{
    public function show(EditorialGuideService $guide): JsonResponse
    {
        $settings = AiPublisherSettings::query()->first();

        return $this->success($settings
            ? $this->format($settings)
            : $this->defaults(),
        );
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company' => ['nullable', 'string', 'max:255'],
            'audience' => ['nullable', 'array'],
            'audience.*' => ['string', 'max:100'],
            'tone' => ['nullable', 'string', 'max:50'],
            'content_rules' => ['nullable', 'array'],
            'content_rules.*' => ['string', 'max:500'],
            'default_status' => ['nullable', 'string', 'in:draft,review,published'],
            'min_content_length' => ['nullable', 'integer', 'min:50', 'max:10000'],
        ]);

        $settings = AiPublisherSettings::query()->first();

        if ($settings) {
            $settings->fill($validated)->save();
        } else {
            $settings = AiPublisherSettings::query()->create($validated + ['tenant_id' => TenantContext::tenantId()]);
        }

        return $this->success($this->format($settings->refresh()), 'Configurações salvas com sucesso.');
    }

    private function format(AiPublisherSettings $settings): array
    {
        return [
            'company' => $settings->company,
            'audience' => $settings->audience,
            'tone' => $settings->tone,
            'content_rules' => $settings->content_rules,
            'default_status' => $settings->default_status,
            'min_content_length' => $settings->min_content_length,
        ];
    }

    private function defaults(): array
    {
        return [
            'company' => config('ai-publisher.editorial.company'),
            'audience' => config('ai-publisher.editorial.audience'),
            'tone' => config('ai-publisher.editorial.tone'),
            'content_rules' => config('ai-publisher.editorial.content_rules'),
            'default_status' => config('ai-publisher.default_status', 'draft'),
            'min_content_length' => config('ai-publisher.min_content_length', 200),
        ];
    }
}
