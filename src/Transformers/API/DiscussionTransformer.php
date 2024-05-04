<?php

namespace Corals\Modules\Messaging\Transformers\API;

use Corals\Foundation\Transformers\APIBaseTransformer;
use Corals\Foundation\Transformers\FractalPresenter;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\User\Transformers\API\SimpleUserPresenter;
use Corals\User\Transformers\API\UserTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscussionTransformer extends APIBaseTransformer
{
    /**
     * @param Discussion $discussion
     * @return array
     * @throws \Throwable
     */
    public function transform(Discussion $discussion)
    {
        $participables = new Collection;

        foreach ($discussion->getReceiverParticipations(user()) as $participations) {
            $participables->push($participations->participable);
        }

        $recivers = (new SimpleUserPresenter)->present($participables)['data'];

        $discussion->load([
            'messages' => fn(HasMany $q) => $q->where('status', '<>', 'draft')->take($q->getModel()->getPerPage())
                ->with(['participations', 'media'])
        ]);

        $userParticipation = $discussion->getUserParticipation();

        $transformedArray = [
            'id' => $discussion->id,
            'receivers' => $recivers,
            'messages' => (new MessagePresenter)->present($discussion->messages)['data'],
            'messages_count' => $discussion->messages_count,
            'created_at' => format_date($discussion->created_at),
            'unread_counts' => $userParticipation->unread_counts,
            'updated_at' => format_date($discussion->updated_at)
        ];

        return parent::transformResponse($transformedArray);
    }

}
