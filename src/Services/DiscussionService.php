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

    public function deleteConversation(Discussion $discussion)
    {
        $lastMessage = $discussion->messages()->first();

        $discussion->getUserParticipation()->update([
            'status' => 'deleted',
            'latest_deleted_message_id' => $lastMessage->id
        ]);
    }

    public function discussionsForUnReadMessages()
    {
        return Discussion::query()
            ->whereHas('participations', function ($query) {
                $query->where('messaging_participations.participable_id', user()->id)
                    ->where('unread_counts', '!=', 0);
            })->get();
    }
}
