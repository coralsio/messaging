<?php


namespace Corals\Modules\Messaging\Services;

use Corals\Foundation\Services\BaseServiceClass;
use Corals\Modules\Messaging\Models\Discussion;

class DiscussionService extends BaseServiceClass
{
    /**
     * @param Discussion $discussion
     * @return mixed|null
     */
    public function markAsRead(Discussion $discussion)
    {
        $lastMessage = $discussion->messages()->first();

        $discussion->getUserParticipation()->update([
            'unread_counts' => 0,
            'last_read' => $lastMessage->created_at
        ]);

        return $this->getModelDetails($discussion->fresh());
    }

    /**
     * @param Discussion $discussion
     */
    public function deleteConversation(Discussion $discussion)
    {
        $lastMessage = $discussion->messages()->first();

        $discussion->getUserParticipation()->update([
            'deleted_at' => now(),
            'latest_deleted_message_id' => $lastMessage->id
        ]);
    }

    /**
     * @return int
     */
    public function unreadMessagesCount(): int
    {
        return Discussion::query()
            ->forUser(user())
            ->sum('messaging_participations.unread_counts');
    }
}
