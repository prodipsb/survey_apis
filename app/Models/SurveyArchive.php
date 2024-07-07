<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyArchive extends Model
{
    use HasFactory;

    protected $table="survey_archives";

    protected $fillable = [
        'bin_number', 
        'bin_holder_name', 
        'bin_holder_address', 
        'division', 
        'circle',
        'commissionerate',
        'zone',
        'email',
        'mobile'
    ];

}
