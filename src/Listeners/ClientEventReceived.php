<?php

namespace Corals\Modules\Messaging\Listeners;

use Corals\Modules\Messaging\Models\Participation;
use Corals\User\Models\User;
use Laravel\Reverb\Events\MessageReceived;

class ClientEventReceived
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageReceived $event): void
    {
        $message = json_decode($event->message);

        if (data_get($message, 'event') !== 'MarkDiscussionAsRead') {
            return;
        }

        $data = json_decode($message->data, true);

        $this->markDiscussionAsRead($data);
    }

    /**
     * @param array $payload
     */
    protected function markDiscussionAsRead(array $payload): void
    {
        $discussionId = data_get($payload, 'did');
        $userId = data_get($payload, 'uid');
        $messageCreatedAt = data_get($payload, 'm_date');

        if (!$discussionId || !$userId) return;

        Participation::query()->where([
            'discussion_id' => $discussionId,
            'participable_type' => getMorphAlias(User::class),
            'participable_id' => $userId
        ])->update([
            'unread_counts' => 0,
            'last_read' => $messageCreatedAt
        ]);
    }
}
