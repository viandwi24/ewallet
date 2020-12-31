<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;

trait UseUuid 
{
    protected static function bootUseUuid() {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}