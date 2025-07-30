<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'incident_id',
        'customer_id',
        'path',
        'created_by',
    ];    
    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
