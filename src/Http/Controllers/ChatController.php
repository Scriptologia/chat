<?php

namespace Scriptologia\Chat\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Scriptologia\Chat\Events\DeliveredMessageEvent;
use Scriptologia\Chat\Events\IsIgnoredEvent;
use Scriptologia\Chat\Events\SendMessageEvent;
use Scriptologia\Chat\Events\DeletedMessageEvent;
use Scriptologia\Chat\Events\ReadedMessageEvent;
use Scriptologia\Chat\Events\TrashedMessageEvent;
use Scriptologia\Chat\Http\Resources\ChatCollection;
use Scriptologia\Chat\Http\Resources\UserCollection;
use Scriptologia\Chat\Models\ChatIgnore;
use Scriptologia\Chat\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('chat::chat');
    }
    /**
     * @return ChatCollection
     */
    public function contacts()
    {
        $auth = auth()->user();
//        dd($auth);
        $messages = ChatMessage::messages($auth)->with([
            'user1' => function($query) use ($auth) {
                $query->where('id', '!=', $auth->id);
            },
            'user2' => function($query) use ($auth) {
                $query->where('id', '!=', $auth->id);
            }
            ])->get();

        $chat = ChatCollection::collection($messages);

        return response()->json($chat);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function allUsers()
    {
        $users = User::get();
        return response()->json($users);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMe()
    {
        $me = auth()->user();
        return response()->json($me);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        if($request->ajax()) {
            $me = auth()->user();
            $users = User::search($request)->where('id', '!=', $me->id)->get();

            $collection = UserCollection::collection($users);
            return response()->json($collection);
        }
    }

    /**
     * @param Request $request
     */
    public function sendMessage(Request $request)
    {
        if($request->ajax()) {
            $me = $request->me;
            $user = $request->user;
            $message = $request->message;
            $reply = $request->reply;
            $reply ? $reply['message'] = mb_substr($reply['message'], 0, 50). '...' : false;
            $message = [
                'message' => $message,
                'reply' => $reply,
                'from' => $me['id'],
                'to' => $user['id'],
                'date' => now(),
                "status"=> 'sended',
                "trashed"=> false
            ];

            $messages = ChatMessage::whereIn('user1_id', [ $user['id'], $me['id'] ])->whereIn('user2_id', [ $user['id'], $me['id'] ])->first();
            if($messages) {
                $old_messages = $messages->messages;
                if(!$old_messages) $old_messages = [];
                array_push($old_messages, $message);
                $messages->messages = $old_messages;
            }
            else {
                $messages = new ChatMessage();
                $messages->messages = [$message];
                $messages->user1_id = $me['id'];
                $messages->user2_id = $user['id'];
            }

            $messages->save();

            broadcast(new SendMessageEvent([ 'message' => $message, 'chat_id' => $messages->id]));
        }
    }

    /**
     * @param Request $request
     */
    public function readMessage(Request $request)
    {
        if($request->ajax()) {
            $me_id = Auth::user()->id;
            $chat_id = $request->chat_id;

            $messages = ChatMessage::where('id', $chat_id)->where(function ($query) use($me_id) {
                $query->where('user1_id', $me_id)->orWhere('user2_id', $me_id);
            })->first();

            if($messages){
               $makeReaded =  function ($it) use($me_id)
                {
                    if ( $it->to === $me_id) $it->status = 'readed';
                    return $it;
                };
                $messages_readed = array_map($makeReaded, $messages->messages);

                $messages->messages = $messages_readed;
                $messages->save();
            }

            $user_id = $messages->user1_id === $me_id ? $messages->user2_id : $messages->user1_id ;
            broadcast( new ReadedMessageEvent(['chat_id' => $chat_id, 'to' => $user_id, 'readed_user_id' => $me_id ]) );
        }
    }

    /**
     * @param Request $request
     */
    public function deliveredMessage(Request $request)
    {
        if($request->ajax()) {
            $me_id = Auth::user()->id;
            $chat_id = $request->chat_id;
            $user = $request->user;
            $message = $request->message;

            $messages = ChatMessage::where('id', $chat_id)->where(function ($query) use($me_id) {
                $query->where('user1_id', $me_id)->orWhere('user2_id', $me_id);
            })->first();

            if($messages){
               $makeReaded =  function ($it) use($me_id, $message, $user)
                {
                    if ( $it->from === $user['id'] && $it->status === 'sended' && (!$message || $it->date === $message['date']) ) $it->status = 'delivered';
                    return $it;
                };
                $messages_delivered = array_map($makeReaded, $messages->messages);

                $messages->messages = $messages_delivered;
                $messages->save();
            }

//            $user_id = $messages->user1_id === $me_id ? $messages->user2_id : $messages->user1_id ;
            broadcast( new DeliveredMessageEvent(['chat_id' => $chat_id, 'from' => $user['id'], 'date' => $message? $message['date'] : $message  ]) );
        }
    }

    /**
     * @param Request $request
     */
    public function trashMessage (Request $request)
    {
        if($request->ajax()) {
            $chat_id = $request->chat_id;
            $message = (object) $request->message;
            $messages = ChatMessage::where('id', $chat_id)->first();
            $makeTrashed =  function ($it) use($message)
            {
                if ( $it->date === $message->date && $it->from === $message->from ) $it->trashed = true;
                return $it;
            };
            $messages_trashed = array_map($makeTrashed, $messages->messages);

            $message->trashed = true;

            $messages->messages = $messages_trashed;
            $messages->save();
            broadcast( new TrashedMessageEvent(['chat_id' => $chat_id, 'trashedMessage' => $message ]) );
        }
    }

    /**
     * @param Request $request
     */
    public function deleteChat (Request $request)
    {
        if($request->ajax()) {
            $chat_id = $request->chat_id;
            $user_id = $request->user_id;
            $chat = ChatMessage::where('id', $chat_id)->first();
            if($chat) {
                $chatDeleted = ['chatDeleted' => $chat, 'user_id' => $user_id];
                $chat->delete();
                broadcast( new DeletedMessageEvent($chatDeleted) );
                return response()->json(['status' => true , 'deletedChat' => $chat, 'user_id' => $user_id ], 200);
            }
        }
    }
    /**
     * @param Request $request
     */
    public function ignoreUser (Request $request)
    {
        if($request->ajax()) {
            $ignore_user_id = $request->ignore_user_id;
            $user_id = Auth::user()->id;
            $ignore = ChatIgnore::create([ 'user_id' =>  $user_id, 'ignore_user_id' => $ignore_user_id ]);
            if($ignore) {
                broadcast(new IsIgnoredEvent(['status' => true , 'ignore_user_id' => $ignore_user_id, 'user_id' => $user_id ])) ;
                return response()->json(['status' => true , 'ignore_user_id' => $ignore_user_id ], 200);
            }
        }
    }
    /**
     * @param Request $request
     */
    public function unIgnoreUser (Request $request)
    {
        if($request->ajax()) {
            $unignore_user_id = $request->unignore_user_id;
            $user_id = Auth::user()->id;
            ChatIgnore::where([ 'user_id' =>  $user_id, 'ignore_user_id' => $unignore_user_id ])->delete();
            broadcast(new IsIgnoredEvent(['status' => false , 'ignore_user_id' => $unignore_user_id, 'user_id' => $user_id ])) ;
            return response()->json(['status' => true , 'unignore_user_id' => $unignore_user_id ], 200);
        }
    }
}
