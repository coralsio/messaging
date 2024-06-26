<?php

namespace Corals\Modules\Messaging\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Corals\Modules\Messaging\Contracts\Participation as ParticipationContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Participation extends BaseModel implements ParticipationContract
{
    use PresentableTrait;
    use LogsActivity;
    use SoftDeletes;

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'messaging.models.participation';

    protected $fillable = ['discussion_id', 'participable_type', 'participable_id', 'last_read', 'status', 'unread_counts', 'latest_deleted_message_id', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'discussion_id' => 'integer',
        'participable_id' => 'integer',
        'unread_counts' => 'integer',
        'last_read' => 'datetime',
    ];

    protected $table = 'messaging_participations';

    public function getModuleName()
    {
        return 'Messaging';
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Discussion relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    /**
     * Participable relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function participable()
    {
        return $this->morphTo();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the participable string info.
     *
     * @return string
     */
    public function stringInfo()
    {
        return $this->participable->getAttribute('name');
    }


    public function canBeRead()
    {
        return in_array($this->status, ['unread', 'important', 'star']);
    }

    public function canBeUnRead()
    {
        return in_array($this->status, ['read', 'important', 'star']);
    }

    public function canBeImportant()
    {
        return in_array($this->status, ['read', 'unread', 'star']);
    }

    public function canBeStar()
    {
        return in_array($this->status, ['read', 'unread', 'important']);
    }
}
