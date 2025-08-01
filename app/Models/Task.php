<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
 
      /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tasks';

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
        'incident_id','description','assigned','target','complete_date','status', 'comment', 'root_causes_id', 'sub_root_causes_id', 'created_at', 'updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'description' => 'string','assigned' => 'string','status' => 'string', 'comment' => 'string','complete_date'=>'datetime:Y-m-d', 'created_at' => 'timestamp', 'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
      'complete_date',  'created_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     * Get the root cause associated with the task.
     */
    public function rootCause()
    {
        return $this->belongsTo(RootCause::class, 'root_causes_id');
    }

    /**
     * Get the sub root cause associated with the task.
     */
    public function subRootCause()
    {
        return $this->belongsTo(SubRootCause::class, 'sub_root_causes_id');
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }
   
}
