<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_key',
        'user_code',
        'sender_id',
    ];

   
}
