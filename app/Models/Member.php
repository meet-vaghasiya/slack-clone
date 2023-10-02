<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Member extends Model
{
    use HasFactory;
    protected $fillable  = ['name', 'workspace_id', 'user_id', 'is_admin', 'avatar'];


    public function getAvatarAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
    function user()
    {
        return $this->belongsTo(User::class);
    }
}
