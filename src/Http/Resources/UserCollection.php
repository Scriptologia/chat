<?php

namespace Scriptologia\Chat\Http\Resources;

//use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Scriptologia\Chat\Models\ChatIgnore;

//use Scriptologia\Chat\Models\ChatMessage;

class UserCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'ignored' => ChatIgnore::where('user_id', auth()->user()->id)->where('ignore_user_id', $this->id)->first() ? true : false
        ];
    }

}
