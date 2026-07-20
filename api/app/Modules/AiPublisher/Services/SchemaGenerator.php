<?php

namespace App\Modules\AiPublisher\Services;

use App\Modules\Post\Http\Requests\StorePostRequest;
use Illuminate\Support\Str;

class SchemaGenerator
{
    public function forPost(): array
    {
        $rules = (new StorePostRequest)->rules();

        return [
            'resource' => 'post',
            'required' => $this->collect($rules, true),
            'optional' => $this->collect($rules, false),
            'statuses' => ['draft', 'review', 'published'],
        ];
    }

    public function forCategory(): array
    {
        return ['resource' => 'category', 'required' => ['name'], 'optional' => ['description', 'parent_id']];
    }

    private function collect(array $rules, bool $required): array
    {
        $fields = [];
        foreach ($rules as $field => $rule) {
            if (! is_array($rule) || Str::contains($field, '.*')) continue;
            $isRequired = in_array('required', $rule);
            if ($required === $isRequired) $fields[] = $field;
        }

        return $fields;
    }
}
