<?php

namespace Corals\Modules\Messaging\DataTables\Scopes;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class DiscussionSearchScope
{
    /**
     * @param EloquentBuilder $query
     * @param $column
     * @param $value
     */
    public function apply(EloquentBuilder $query, $column, $value): void
    {
        $query->whereHas('participations.participable', function (EloquentBuilder $q) use ($value) {
            $q->when(user(), fn(EloquentBuilder $q) => $q->where('users.id', '<>', user()->id))
                ->whereAny(['users.name', 'users.last_name', 'email'], 'like', "%$value%");
        });
    }
}
