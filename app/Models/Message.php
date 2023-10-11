<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['content', 'sender_id', 'receiver_id', 'parent_message_id', 'created_at', 'updated_at', 'workspace_id'];


    public function member()
    {
        return $this->belongsTo(Member::class, 'sender_id');
    }
}
