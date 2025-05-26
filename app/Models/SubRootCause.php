<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubRootCause extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sub_root_causes';

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
        'root_cause_id',
        'name',
        'description',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'root_cause_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'status' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * Get the root cause that owns this sub root cause.
     */
    public function rootCause()
    {
        return $this->belongsTo(RootCause::class);
    }

    /**
     * Get the incidents with this sub root cause.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}