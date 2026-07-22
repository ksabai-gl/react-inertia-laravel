<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardRecord extends Model
{
    protected $fillable = [
        'record_key',
        'name',
        'phone',
        'module',
        'status',
        'region',
        'updated_at_source',
    ];

    protected $casts = [
        'updated_at_source' => 'datetime',
    ];
}
