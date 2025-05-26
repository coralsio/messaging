<?php


namespace Corals\Modules\Messaging\Services;

use Corals\Foundation\Services\BaseServiceClass;
use Corals\Modules\Messaging\Events\MessageReceived;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\Modules\Messaging\Models\Message;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MessageService extends BaseServiceClass
{
    /**
     * @param Request $request
     * @param $additionalData
     */
    public function preStore($request, &$additionalData)
    {
        $secondParticipationId = $request->get('second_participation_id');

        if ($request->filled('discussion_id')) {
            $discussion = tap(
                Discussion::find($request->get('discussion_id'))
            )->restoreAllParticipations();

            return;
        }

        $participableType = $request->get('participable_type', getMorphAlias(user()));
        $secondParticipationType = $request->get('second_participation_type', getMorphAlias(user()));

        $participableId = $request->get('participable_id', user()?->id);

        $participableObject = Relation::getMorphedModel($participableType)::find($participableId);

        //check if they have already chatted.
        $discussion = $participableObject->discussions()
            ->whereHas(
                'participations', fn($q) => $q->withTrashed()
                ->where([
                    'messaging_participations.participable_id' => $secondParticipationId,
                    'messaging_participations.participable_type' => $secondParticipationType
                ])
            )->select('messaging_discussions.id')
            ->first();

        if ($discussion) {
            // check if the discussion is already deleted, we need to restore it.
            $discussion->restoreAllParticipations();
            $additionalData['discussion_id'] = $discussion->id;
            return;
        }

        // if the discussion is not preseting in the request
        // means the user is chating with someone didn't chat with before.
        $discussionData = [];


        if ($request->filled('discussion_properties')) {
            $discussionData['properties'] = $request->get('discussion_properties');
        }

        $discussion = Discussion::query()->create($discussionData);

        $discussion->participations()->createMany([
            [
                'participable_type' => $participableType,
                'participable_id' => $participableId
            ],
            [
                'participable_type' => $secondParticipationType,
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
            ->where(function (Builder $q) use ($message) {
                $q->where('id', '<', $message->id)
                    ->when($message->userParticipation()?->latest_deleted_message_id, function (Builder $q, $lastDeletedMsgId) {
                        $q->where('id', '>', $lastDeletedMsgId);
                    });
            })->paginate();

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
