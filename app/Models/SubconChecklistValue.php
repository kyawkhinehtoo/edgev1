<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SubconChecklist;
use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use App\Models\SubconChecklistValueHistory;
class SubconChecklistValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subcon_checklist_values';

    protected $fillable = [
        'subcon_checklist_id',
        'customer_id',
        'task_id',
        'title',
        'attachment',
        'status',
        'current_actor_user_id',
        'last_status_changed_at',
    ];

    public function checklist()
    {
        return $this->belongsTo(SubconChecklist::class, 'subcon_checklist_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function currentActor()
    {
        return $this->belongsTo(User::class, 'current_actor_user_id');
    }

    public function histories()
    {
        return $this->hasMany(SubconChecklistValueHistory::class, 'checklist_value_id');
    }
}