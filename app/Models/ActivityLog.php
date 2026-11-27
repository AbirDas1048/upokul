<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'user_name', 'action', 'description', 'ip_address', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];
}
