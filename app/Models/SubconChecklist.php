<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubconChecklist extends Model
{
    use HasFactory;

    protected $table = 'subcon_checklists';

    protected $fillable = [
        'name',
        'has_attachment',
        'group_id',
        'service_type',
        'remarks',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(SubconChecklistsGroup::class, 'group_id');
    }
     public function values()
    {
        return $this->hasMany(SubconChecklistValue::class, 'subcon_checklist_id');
    }
}