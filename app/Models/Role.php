<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string     $name
 * @property int        $created_at
 * @property int        $updated_at
 */
class Role extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

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
        'permissions',
        'customer_status',
        'read_customer',
        'read_incident',
        'write_incident',
        'edit_invoice',
        'bill_generation',
        'bill_receipt',
        'delete_customer',
        'radius_read',
        'radius_write',
        'incident_report',
        'bill_report',
        'radius_report',
        'incident_only',
        'read_only_ip',
        'add_ip',
        'edit_ip',
        'delete_ip',
        'ip_report',
        'delete_invoice',
        'enable_customer_export',
        'activity_log',
        'system_setting',
        'admin_panel',
        'customer_panel',
        'incident_panel',
        'billing_panel',
        'report_panel',
        'limit_region',
        'dn_panel',
        'installation_supervisor',
        'product_catalog',
        'smtp_setting',
        'incident_supervisor',
        'installation_oss',
        'incident_oss',
        'service_request',
        'check_sn',
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'permissions' => 'array',
        'customer_status' => 'array',
        'read_customer' => 'integer',
        'read_incident' => 'integer',
        'write_incident' => 'integer',
        'edit_invoice' => 'integer',
        'bill_generation' => 'integer',
        'bill_receipt' => 'integer',
        'radius_read' => 'integer',
        'radius_write' => 'integer',
        'incident_report' => 'integer',
        'bill_report' => 'integer',
        'radius_report' => 'integer',
        'incident_only' => 'integer',
        'read_only_ip' => 'integer',
        'add_ip' => 'integer',
        'edit_ip' => 'integer',
        'delete_ip' => 'integer',
        'ip_report' => 'integer',
        'delete_invoice' => 'integer',
        'admin_panel'=>'integer',
        'customer_panel'=>'integer',
        'incident_panel'=>'integer',
        'billing_panel'=>'integer',
        'report_panel'=>'integer',
        'enable_customer_export' => 'integer',
        'limit_region' => 'integer',
        'dn_panel' => 'integer',
        'installation_supervisor' => 'integer',
        'smtp_setting' => 'integer',
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
        'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations ...
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
        // 'role' is the foreign key in the users table
        // 'id' is the primary key in the roles table
    }
    public function townships()
    {
        return $this->belongsToMany(Township::class, 'role_township')
                    ->withTimestamps();
    }
}
