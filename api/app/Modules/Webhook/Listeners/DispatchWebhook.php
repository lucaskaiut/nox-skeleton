<?php

namespace App\Modules\Webhook\Listeners;

use App\Modules\Post\Events\PostCreated;
use App\Modules\Post\Events\PostDeleted;
use App\Modules\Post\Events\PostPublished;
use App\Modules\Post\Events\PostUpdated;
use App\Modules\Webhook\Enums\WebhookEvent;
use App\Modules\Webhook\Jobs\SendWebhook;
use App\Modules\Webhook\Services\WebhookService;

class DispatchWebhook
{
    public function __construct(private readonly WebhookService $service) {}

    public function handle(PostCreated|PostUpdated|PostDeleted|PostPublished $event): void
    {
        $eventName = match (true) {
            $event instanceof PostCreated => WebhookEvent::POST_CREATED->value,
            $event instanceof PostUpdated => WebhookEvent::POST_UPDATED->value,
            $event instanceof PostDeleted => WebhookEvent::POST_DELETED->value,
            $event instanceof PostPublished => WebhookEvent::POST_PUBLISHED->value,
        };

        $webhooks = $this->service->getActiveByEvent($eventName);

        foreach ($webhooks as $webhook) {
            SendWebhook::dispatch($webhook, $event->post->load(['author', 'categories', 'tags']));
        }
    }
}
