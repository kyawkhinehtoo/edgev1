<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubconChecklistsGroup extends Model
{
    protected $table = 'subcon_checklists_group';

    protected $fillable = [
        'name',
        'description',
        'category',
        'required',
    ];
    protected $casts = [
        'required' => 'boolean',
    ];
    public function checklists()
    {
        return $this->hasMany(SubconChecklist::class, 'group_id');
    }
}
