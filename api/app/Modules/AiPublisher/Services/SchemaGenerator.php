<?php

namespace App\Modules\AiPublisher\Services;

use App\Modules\Post\Http\Requests\StorePostRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SchemaGenerator
{
    /**
     * Gera o schema de um recurso a partir das regras de validação do FormRequest.
     *
     * @return array{resource: string, required: list<string>, optional: list<string>, statuses: list<string>}
     */
    public function forPost(): array
    {
        $rules = (new StorePostRequest)->rules();

        return [
            'resource' => 'post',
            'required' => $this->requiredFields($rules),
            'optional' => $this->optionalFields($rules),
            'statuses' => ['draft', 'review', 'published'],
        ];
    }

    /**
     * @return array{resource: string, required: list<string>, optional: list<string>}
     */
    public function forCategory(): array
    {
        return [
            'resource' => 'category',
            'required' => ['name'],
            'optional' => ['description', 'parent_id'],
        ];
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return list<string>
     */
    private function requiredFields(array $rules): array
    {
        $fields = [];

        foreach ($rules as $field => $rule) {
            if (is_array($rule) && in_array('required', $rule)) {
                $fields[] = $this->normalizeField($field);
            }
        }

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return list<string>
     */
    private function optionalFields(array $rules): array
    {
        $fields = [];

        foreach ($rules as $field => $rule) {
            if (is_array($rule) && ! in_array('required', $rule) && ! Str::contains($field, '.*')) {
                $fields[] = $this->normalizeField($field);
            }
        }

        return $fields;
    }

    private function normalizeField(string $field): string
    {
        return str_replace('.*', '', $field);
    }
}
