<?php

namespace Scriptologia\Chat\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory, Searchable;

    protected $guarded = ['user1', 'user2', 'messages'];
    protected $casts = [
        'user1' =>'integer',
        'user2' =>'integer',
        'messages' => 'object'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id' , 'id');
    }

    /***
     * @param $query
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @return mixed
     */

    public function scopeMessages($query, $user = null)
    {
        if( !$user)  { $query; }
        else {  $query->where('user1_id', $user->id)->orWhere('user2_id', $user->id);  }
    }
}
