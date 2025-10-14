<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteUsage extends Model
{
    protected $table = 'route_usages';

    protected $fillable = [
        'month_key', 'count', 'limit', 'warn_ratio',
    ];

    protected $casts = [
        'count'      => 'integer',
        'limit'      => 'integer',
        'warn_ratio' => 'float',
    ];
}
