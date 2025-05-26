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
        'service_type',
        'remarks',
        'status',
    ];
}