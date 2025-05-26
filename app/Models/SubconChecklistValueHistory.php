<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SubconChecklistValue;
use App\Models\User;
class SubconChecklistValueHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subcon_checklist_value_histories';

    protected $fillable = [
        'checklist_value_id',
        'title',
        'attachment',
        'action',
        'actor_id',
        'acted_at',
    ];

    public function checklistValue()
    {
        return $this->belongsTo(SubconChecklistValue::class, 'checklist_value_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}