<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberMaker extends Model
{
    use HasFactory;
    protected $fillable = ['number','number_id','user_id','status','dncr_file'];
}
