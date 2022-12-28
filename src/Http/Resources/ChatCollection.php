<?php

namespace Scriptologia\Chat\Http\Resources;

//use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

//use Scriptologia\Chat\Models\ChatMessage;

class ChatCollection extends JsonResource
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
            'messages' => $this->messages? $this->messages : [],
            'user' => $this->getRelation('user1') ?? $this->getRelation('user2'),
            'countNew' => 0,
            'active' => false
        ];
    }

}
