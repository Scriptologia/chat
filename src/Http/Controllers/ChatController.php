<?php

namespace Scriptologia\Chat\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat::chat');
    }
}
