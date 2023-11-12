<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyItem extends Model
{
    use HasFactory;

    // protected $table="survey_items";

    protected $fillable = [ 
        'survey_id',
        'url'
    ];



 
}
