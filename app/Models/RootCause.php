<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RootCause extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'root_causes';

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
        'name',
        'description',
        'status',
        'is_installation',
        'is_maintenance',
        'is_pending',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'status' => 'string',
        'is_installation' => 'boolean',
        'is_maintenance' => 'boolean',
        'is_pending' => 'boolean',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * Get the sub root causes for this root cause.
     */
    public function subRootCauses()
    {
        return $this->hasMany(SubRootCause::class);
    }

    /**
     * Get the incidents with this root cause.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}