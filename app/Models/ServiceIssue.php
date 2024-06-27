<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceIssue extends Model
{
    use HasFactory;

    protected $table='service_issues';
    protected $with=['issue'];

    protected $fillable = [
        'service_id',
        'issue_id'
    ];

    public function issue(){
        return $this->belongsTo(DeviceServiceIssue::class);
    }
}
