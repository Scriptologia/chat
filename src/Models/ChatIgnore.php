<?php

namespace Scriptologia\Chat\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatIgnore extends Model
{
    use HasFactory;

    protected $fillable  = ['user_id', 'ignore_user_id'];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ignoreUsers()
    {
        return $this->belongsTo(User::class, 'ignore_user_id' , 'id');
    }
}
