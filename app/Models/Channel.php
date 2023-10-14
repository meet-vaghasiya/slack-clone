<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name', 'creator_id', 'workspace_id', 'is_private', 'topic', 'description'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
    public function members()
    {
        return $this->belongsToMany(Member::class)->withTimestamps();
    }
}
