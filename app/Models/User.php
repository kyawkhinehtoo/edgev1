<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    /**
     * Role 
     * 0 - super admin
     * 1 - admin 
     * 2 - technical 
     * 3 - sale
     * 4 - billing
     * 5 - subcom
     * 
     **/
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'user_type',
        'password',
        'disabled',
        'isp_id',
        'partner_id',
        'last_login_ip',
        'last_login_time'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
        // 'role' is the foreign key in the users table
        // 'id' is the primary key in the roles table
    }
    
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
    public function isp()
    {
        return $this->belongsTo(Isp::class);
    }
    public function subcom()
    {
        return $this->belongsTo(Subcom::class);
    }
    
   
}
