<?php


namespace Corals\Modules\Messaging\Services;

use Corals\Foundation\Services\BaseServiceClass;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\Modules\Messaging\Models\Participation;

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
}