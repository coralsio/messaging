<?php


namespace Corals\Modules\Messaging\Services;

use Corals\Foundation\Services\BaseServiceClass;
use Corals\Modules\Messaging\Events\MessageReceived;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\Modules\Messaging\Models\Message;
use Exception;
use Illuminate\Http\Request;

class MessageService extends BaseServiceClass
{
    /**
     * @param Request $request
     * @param $additionalData
     */
    public function preStore(Request $request, &$additionalData)
    {
        if ($request->filled('discussion_id')) {
            return;
        }

        $secondParticipationId = $request->get('second_participation_id');

        //check if they have already chatted.
        $discussion = user()->discussions()
            ->whereHas(
                'participations', fn($q) => $q->where('messaging_participations.participable_id', $secondParticipationId)
            )->select('messaging_discussions.id')
            ->first();

        if ($discussion) {
            $additionalData['discussion_id'] = $discussion->id;
            return;
        }

        // if the discussion is not preseting in the request
        // means the user is chating with someone didn't chat with before.
        $discussion = Discussion::query()->create();

        $participableType = getMorphAlias(user());

        $discussion->participations()->createMany([
            [
                'participable_type' => $participableType,
                'participable_id' => user()->id
            ],
            [
                'participable_type' => $participableType,
                'participable_id' => $secondParticipationId
            ]
        ]);

        $additionalData['discussion_id'] = $discussion->id;

    }

    /**
     * @param Message $message
     * @return array|mixed
     * @throws Exception
     */
    public function fetchMoreMessages(Message $message)
    {
        $messages = Message::query()
            ->where('status', '<>', 'draft')
            ->orderBy('created_at', 'desc')
            ->with(['participations', 'media'])
            ->where('discussion_id', $message->discussion_id)
            ->where('id', '<', $message->id)
            ->paginate();

        return $this->getPresenter()->present($messages);
    }

    /**
     * @param Message $message
     * @return array|mixed
     * @throws Exception
     */
    public function broadcastMessage(Message $message)
    {
        $message->update(['status' => 'active']);
        broadcast(new MessageReceived($message));

        return $this->getPresenter()->present($message)['data'];
    }
}
