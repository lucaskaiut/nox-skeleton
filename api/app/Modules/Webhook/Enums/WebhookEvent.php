<?php

namespace App\Modules\Webhook\Enums;

enum WebhookEvent: string
{
    case POST_CREATED = 'post.created';
    case POST_UPDATED = 'post.updated';
    case POST_DELETED = 'post.deleted';
    case POST_PUBLISHED = 'post.published';

    public function label(): string
    {
        return match ($this) {
            self::POST_CREATED => 'Post criado',
            self::POST_UPDATED => 'Post atualizado',
            self::POST_DELETED => 'Post removido',
            self::POST_PUBLISHED => 'Post publicado',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
