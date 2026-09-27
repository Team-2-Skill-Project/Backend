<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_chat_id',
        'sender',
        'content',
        'supported_actions',
    ];

    protected $casts = [
        'supported_actions' => 'array',
    ];

    public function chat()
    {
        return $this->belongsTo(MentorChat::class, 'mentor_chat_id');
    }
}
