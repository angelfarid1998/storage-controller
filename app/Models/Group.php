<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'quota'
    ];

    //
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }
}
