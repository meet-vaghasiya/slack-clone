<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Http\Requests\MessageStoreRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $workspaceId)
    {
        $user1Id = User::getMemberForWorkspace($workspaceId)->id;
        $user2Id = $request->other_user_id;
        // Retrieve one-to-one messages between user1 and user2
        $messages = Message::with('member')->where('workspace_id', $workspaceId)->where(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user1Id)
                ->where('receiver_id', $user2Id);
        })->orWhere(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user2Id)
                ->where('receiver_id', $user1Id);
        })->get();

        // Return the list of messages in the response
        return response()->json(['data' => $messages], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(MessageStoreRequest $request, $workspaceId)
    {
        $message = Message::create([
            'content' => $request->content,
            'workspace_id' => $workspaceId,
            'sender_id' => User::getMemberForWorkspace($workspaceId)->id,
            'receiver_id' => $request->receiver_id,
            'parent_message_id' => $request->parent_message_id ?? null
        ]);
        $message->load('member');
        broadcast(new MessageEvent($message));
        return response()->json(['message' => 'Message created successfully', 'data' => $message], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
