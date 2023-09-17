<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;
    protected  $fillable = ['name', 'user_id'];

    function user()
    {
        return $this->belongsTo(User::class);
    }
    function members()
    {
        return $this->hasMany(Member::class);
    }
}
