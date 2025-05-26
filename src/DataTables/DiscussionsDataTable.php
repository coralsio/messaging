<?php

namespace Corals\Modules\Messaging\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Messaging\DataTables\Scopes\DiscussionSearchScope;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\Modules\Messaging\Transformers\DiscussionTransformer;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;

class DiscussionsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('messaging.models.discussion.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new DiscussionTransformer());
    }

    /**
     * @param Discussion $model
     * @param string|null $lastMessageCreatedAt
     * @return mixed
     */
    public function query(Discussion $model, string $lastMessageCreatedAt = null)
    {
        return $this->baseQuery($model, $lastMessageCreatedAt)
            ->when(!isSuperUser(), fn(Builder $q) => $q->forUser(user()))
            ->withCount(['messages' => function (Builder $builder) {
                $builder->where('status', '<>', 'draft');
            }]);
    }

    protected function baseQuery(Discussion $model, $lastMessageCreatedAt = null)
    {
        $subQueryLastMessage = \Corals\Modules\Messaging\Models\Message::query()
            ->select('messaging_messages.created_at')
            ->whereColumn('messaging_discussions.id', 'messaging_messages.discussion_id')
            ->limit(1)
            ->orderBy('created_at', 'desc');

        return $model->newQuery()
            ->select('messaging_discussions.*')
            ->when($lastMessageCreatedAt, function (Builder $builder, $lastMessageCreatedAt) use ($subQueryLastMessage) {
                $builder->selectSub($subQueryLastMessage, 'last_message')
                    ->whereRaw("({$subQueryLastMessage->toSql()}) < ?", [$lastMessageCreatedAt]);
            })->orderBy($subQueryLastMessage, 'desc');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['visible' => false],
            'creator' => ['title' => trans('Messaging::attributes.discussion.creator')],
            'subject' => ['title' => trans('Messaging::attributes.discussion.subject')],
            'participations' => ['title' => trans('Messaging::attributes.discussion.participations')],
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }

    /**
     * @return array[]
     */
    public function getFilters()
    {
        return [
            'search' => [
                'title' => 'Search',
                'class' => 'col-md-3',
                'type' => 'text',
                'condition' => 'like',
                'active' => true,
                'builder' => DiscussionSearchScope::class
            ]
        ];
    }
}
