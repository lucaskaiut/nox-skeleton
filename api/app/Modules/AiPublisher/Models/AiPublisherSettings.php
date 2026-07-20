<?php

namespace App\Modules\AiPublisher\Models;

use App\Modules\Tenant\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AiPublisherSettings extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company', 'audience', 'tone', 'content_rules',
        'default_status', 'min_content_length',
    ];

    protected function casts(): array
    {
        return [
            'audience' => 'array',
            'content_rules' => 'array',
            'min_content_length' => 'integer',
        ];
    }
}
