<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = ['user1', 'user2', 'messages'];
    protected $casts = [
        'messages' => 'object'
    ];
}
