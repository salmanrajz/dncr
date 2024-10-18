<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerData extends Model
{
    use HasFactory;
    protected $table = 'customer_data'; // Specify the table

    protected $fillable = [
        'number',
        'prefix',
        'isWhatsApp',
        'isDncr',
        'status','master_number','user_id'
    ];
}
