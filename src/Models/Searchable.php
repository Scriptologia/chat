<?php

namespace Scriptologia\Chat\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Searchable
{
    public function scopeSearch(Builder $query, Request $request) {
        $fields = config('chat.searchable');
        $value = $request->search;
        return $query->where( function ($query)  use($value, $fields) {
            foreach($fields as $field)
            {
                $query->orWhere($field, 'LIKE', "%{$value}%");
            }
         });
    }
}