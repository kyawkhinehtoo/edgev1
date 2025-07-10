<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{

     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'incidents';

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
        'code',
        'priority',
        'incharge_id',
        'customer_id',
        'package_id',
        'type',
        'topic',
        'suspense_from',
        'suspense_to',
        'resume',
        'new_address',
        'location',
        'termination',
        'date',
        'close_date',
        'time',
        'close_time',
        'description',
        'status',
        'closed_by',
        'root_cause_id',
        'sub_root_cause_id',
        'rca_notes',
        
        'new_partner_id',
        'new_pop_id',
        'new_pop_device_id',
        'new_dn_splitter_id',
        'new_sn_splitter_id',
        'new_port_number',
        'relocation_service_id',
        'start_date',
        'created_at',
        'updated_at',
        'supervisor_id',
        'assigned_by'
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
        'code'=> 'string',
        'priority'=> 'string',
        'incharge_id'=> 'string',
        'customer_id'=> 'string',
        'package_id'=> 'string',
        'type'=> 'string',
        'topic'=> 'string',
        'suspense_from'=> 'timestamp',
        'suspense_to'=> 'timestamp',
        'resume'=> 'timestamp',
        'new_address'=> 'string',
        'location'=> 'string',
        'termination'=> 'timestamp',
        'date'=> 'datetime:d-m-Y',
        'close_date'=> 'datetime:d-m-Y',
        'time'=> 'timestamp',
        'close_time'=> 'timestamp',
        'description' => 'string',
        'status' => 'string', 
        'closed_by'=> 'string',
        'root_cause_id' => 'integer',
        'sub_root_cause_id' => 'integer',
        'rca_notes' => 'string',
        'new_sn_splitter_id' => 'integer',
        'new_port_number' => 'string',
        'new_pop_device_id' => 'integer',
        'new_pop_id' => 'integer',
        'new_dn_splitter_id' => 'integer',
        'start_date => datetime:d-m-Y',
        'created_at' => 'timestamp', 
        'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'suspense_from','suspense_to','resume','termination', 'created_at', 'updated_at'
    ];
    //  protected $casts = [
    //     'ftth_id' => 'string',
    //     'name' => 'string',
    //     'phone_1' => 'string',
    //     'created_at' => 'timestamp',
    //     'updated_at' => 'timestamp',
    //     'order_date' => 'datetime:Y-m-d',
    //     'service_activation_date' => 'datetime:Y-m-d',
    //     'prefer_install_date' => 'datetime:Y-m-d',
    //     'installation_date' => 'datetime:Y-m-d',
    //     'installation_reappointment_date' => 'datetime:Y-m-d',
    //     'subcom_assign_date' => 'datetime:Y-m-d H:i:s',
    //     'way_list_date' => 'datetime:Y-m-d',
    //     'service_termination_date' => 'datetime:Y-m-d',
    // ];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get the root cause associated with the incident.
     */
    public function rootCause()
    {
        return $this->belongsTo(RootCause::class);
    }

    /**
     * Get the sub root cause associated with the incident.
     */
    public function subRootCause()
    {
        return $this->belongsTo(SubRootCause::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
        // Add these relationship methods to the model
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
