<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Customer extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

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

        'ftth_id',
        'isp_ftth_id',
        'name',
        'phone_1',
        'address',
        'location',
        'order_date',
        'installation_date',
        'prefer_install_date',
        'service_activation_date',
        'township_id',
        'package_id',
        'status_id',
        'subcom_id',
        'fiber_distance',
        'installation_remark',
        'onu_serial',
        'onu_power',
        'bundles',
        'gpon_ontid',
        'partner_id',
        'created_by',
        'isp_id',
        'order_remark',
        'installation_status',
    
        'installation_reappointment_date',
        'subcom_assign_date',
        'way_list_date',
        'created_at',
        'updated_at',
        'deleted',
    ];

    protected $casts = [
        'ftth_id' => 'string',
        'name' => 'string',
        'phone_1' => 'string',
        'address' => 'string',
        'location' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'order_date' => 'datetime:Y-m-d',
        'service_activation_date' => 'datetime:Y-m-d',
        'prefer_install_date' => 'datetime:Y-m-d',
        'installation_date' => 'datetime:Y-m-d',
        'installation_reappointment_date' => 'datetime:Y-m-d',
        'subcom_assign_date' => 'datetime:Y-m-d H:i:s',
        'way_list_date' => 'datetime:Y-m-d',
        'service_termination_date' => 'datetime:Y-m-d',
    ];

    protected $dates = [
        'order_date',
        'installation_date',
        'prefer_install_date',
        'service_termination_date',
        'created_at',
        'updated_at'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

  
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations 
    // public function township()
    // {
    //     return $this->hasOne(Township::class);
    // }
    public function township()
    {
        return $this->belongsTo(Township::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
     public function partner()
     {
         return $this->belongsTo(Partner::class, 'partner_id');
     }
     public function isp()
     {
         return $this->belongsTo(Isp::class, 'isp_id');
     }

     public function subcon()
     {
         return $this->belongsTo(Subcom::class, 'subcom_id');
     }
    public function getTableColumns()
    {
        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $column_array = array();
        $excluded_columns = ['id', 'created_at', 'updated_at', 'deleted'];
        
        foreach ($columns as $key => $value) {
            if (!in_array($value, $excluded_columns)) {
                array_push($column_array, ['id' => $key, 'name' => $value]);
            }
        }
        usort($column_array, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return $column_array;
    }
    public function getTableColumnForOther()
    {
        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $column_array = array();
        $excluded_columns = ['id', 
        'created_at', 
        'updated_at',
        'phone_2',
        'subcom_id',
        'partner_id',

        'isp_id',

        'gpon_ontid',
        'port_balance',
        'fc_used',
        'fc_damaged',
        'vlan',
        'splitter_no',
        'fiber_distance',
        'installation_remark',
        'service_activation_date',
        'project_id',
        'deleted',
        'created_by',
        'customer_type',
        'installation_status',
        'subcom_assign_date',
        'installation_reappointment_date',
        'service_termination_date',
    ];
        
        foreach ($columns as $key => $value) {
            if (!in_array($value, $excluded_columns)) {
                array_push($column_array, ['id' => $key, 'name' => $value]);
            }
        }   
        usort($column_array, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return $column_array;
    }
    public function snPort()
    {
        return $this->hasOne(SnPort::class);
    }
}
