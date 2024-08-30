<?php

namespace Corals\Modules\Messaging\Transformers\API;

use Corals\Foundation\Transformers\APIBaseTransformer;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\Modules\Messaging\Models\Message;
use Corals\Modules\Messaging\Models\Participation;
use Corals\User\Transformers\API\SimpleUserPresenter;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MessageTransformer extends APIBaseTransformer
{
    /**
     * @param Discussion $discussion
     * @return array
     * @throws \Throwable
     */
    public function transform(Message $message)
    {
        $seen = $message->recipients->every(
            fn(Participation $participation) => $participation->last_read && $participation
                    ->last_read
                    ->gte($message->created_at)
        );

        $media = $message->getMedia($message->mediaCollectionName)->map(fn(Media $m) => [
            'id' => $m->id,
            'url' => getMediaPublicURL($m),
            'mime_type' => $m->mime_type,
            'file_name' => $m->file_name,
            'size' => $m->size
        ])->toArray();


        $transformedArray = [
            'id' => $message->id,
            'body' => $message->body,
            'discussion_id' => $message->discussion_id,
            'sender' => (new SimpleUserPresenter)->present($message->participable)['data'],
            'created_at_for_humans' => $message->created_at->diffForHumans(short: true, syntax: true),
            'formatted_created_at' => format_date($message->created_at, 'h:i a'),
            'seen' => $seen,
            'media' => $media,
            'created_at' => $message->created_at->toDateTimeString(),
            'updated_at' => format_date($message->updated_at)
        ];

        return parent::transformResponse($transformedArray);
    }
}
