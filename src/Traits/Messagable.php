<?php

namespace Corals\Modules\Messaging\Traits;

use Corals\Modules\Messaging\Models;
use Illuminate\Database\Eloquent\Builder;

trait Messagable
{
    protected array $inboxStatuses = ['read', 'unread', 'important', 'star'];

    /**
     * Discussions relationship with optional status filter and eager loading.
     *
     * @param string|null $status
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany|\Illuminate\Support\Collection
     */
    public function discussions(string $status = null, array $params = [])
    {
        $morphToMany = $this->morphToMany(
            Models\Discussion::class,
            'participable',
            'messaging_participations'
        );

        if ($status) {
            $morphToMany->wherePivot('status', $status);
        } else {
            $morphToMany->wherePivotIn('status', $this->inboxStatuses);
        }

        return $params['getData'] ?? false ? $morphToMany->getResults() : $morphToMany;
    }

    /**
     * Participations relationship.
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany|\Illuminate\Support\Collection
     */
    public function participations(array $params = [])
    {
        $relation = $this->morphMany(Models\Participation::class, 'participable');

        return $params['getData'] ?? false ? $relation->getResults() : $relation;
    }

    /**
     * Messages relationship.
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany|\Illuminate\Support\Collection
     */
    public function messages(array $params = [])
    {
        $relation = $this->morphMany(Models\Message::class, 'participable');

        return $params['getData'] ?? false ? $relation->getResults() : $relation;
    }

    /**
     * Count of new messages.
     *
     * @param array $params
     * @return int
     */
    public function newMessagesCount(array $params = []): int
    {
        return $this->discussionsWithNewMessages()->count();
    }

    /**
     * Get discussions with new (unread) messages.
     *
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function discussionsWithNewMessages(array $params = [])
    {
        $participationsTable = 'messaging_participations';
        $discussionsTable = 'messaging_discussions';

        return $this->discussions()->where(function (Builder $query) use ($participationsTable, $discussionsTable) {
            $query->whereNull("$participationsTable.last_read")
                ->orWhere(
                    "$discussionsTable.updated_at",
                    '>',
                    $this->getConnection()->raw("$participationsTable.last_read")
                );
        })->get();
    }
}
