<?php

namespace Corals\Modules\Messaging\Observers;

use Corals\Modules\Messaging\Events\MessageReceived;
use Corals\Modules\Messaging\Models\Message;
use Illuminate\Support\Facades\DB;

class MessageObserver
{
    /**
     * @param Message $message
     */
    public function created(Message $message)
    {
        $message->participations()
            //exclude the message creator
            ->where('participable_type', '=', $message->participable_type)
            ->where('participable_id', '<>', $message->participable_id)
            ->update([
                'unread_counts' => DB::raw('messaging_participations.unread_counts + 1')
            ]);

        broadcast(new MessageReceived($message));
    }

}
