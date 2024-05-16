<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPost extends Model
{
    use HasFactory;


    protected $table = 'post_reports';

    protected $fillable = [
        'post_id',
        'user',
        'report',
        'status'
      
    ];


}
