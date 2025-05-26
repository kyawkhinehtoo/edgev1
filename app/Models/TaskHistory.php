<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'task_histories';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'incident_id',
        'assigned',
        'target',
        'status',
        'description',
        'comment',
        'root_causes_id',
        'sub_root_causes_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'description' => 'string',
        'assigned' => 'integer',
        'status' => 'string',
        'comment' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'target'
    ];

    /**
     * Get the task that owns the history.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the incident that owns the history.
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the root cause associated with the history.
     */
    public function rootCause()
    {
        return $this->belongsTo(RootCause::class, 'root_causes_id');
    }

    /**
     * Get the sub root cause associated with the history.
     */
    public function subRootCause()
    {
        return $this->belongsTo(SubRootCause::class, 'sub_root_causes_id');
    }
}
