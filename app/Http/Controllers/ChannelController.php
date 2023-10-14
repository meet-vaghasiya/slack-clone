<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChannelCreateRequest;
use App\Http\Requests\UpdateChannelRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\Channel;
use App\Models\Member;
use App\Models\User;
use App\Models\Workspace;
use App\Rules\MembersBelongToWorkspace;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function isValid(Request $request, $workspaceId)
    {
        $isExist =   Channel::where(['workspace_id' => $workspaceId, 'name' => $request->name])->exists();
        return response()->json(['isValid' => !$isExist]);
    }

    public function index(Workspace $workspace)
    {
        $channel =  Channel::select(['id', 'name'])->where('workspace_id', $workspace->id)->get();
        return response()->json(['data' => $channel], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ChannelCreateRequest $request, Workspace $workspace)
    {
        $memberId = User::getMemberForWorkspace($workspace->id)->id;
        try {

            return DB::transaction(function () use ($workspace, $request, $memberId) {
                $channel =    Channel::create(
                    [
                        'name' => $request->name,
                        'workspace_id' => $workspace->id,
                        'creator_id' => $memberId,
                        'is_private' => $request->is_private,
                    ]
                );

                if ($channel->is_private) {
                    $channel->members()->attach($memberId);
                } else {
                    $channel->members()->attach(Member::where('workspace_id', $workspace->id)->pluck('id')->toArray());
                }
                return response()->json(['message' => 'Channel added in given workspace', 'data' => $channel], 200);
            });
        } catch (Exception $e) {
            return response()->json(['message' => 'Adding member failed', 'error' => $e->getMessage()], 500);
        }
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
    public function show(Channel $channel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Channel $channel)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChannelRequest $request, Workspace $workspace, Channel $channel)
    {
        $data = $request->only(['name', 'topic', 'description']);
        try {
            $channel->update($data);
        } catch (\Exception $e) {
            // Return an error response if the update fails
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // Return a response indicating success
        return response()->json(['message' => 'Channel updated successfully', 'data' => $channel]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Channel $channel)
    {
        //
    }

    public function addMembers(Workspace $workspace, Channel $channel, Request $request)
    {
        $validatedData = $request->validate([
            'member_ids' => ['required', 'array', new MembersBelongToWorkspace($workspace)],
            'member_ids.*' => 'exists:members,id',
        ]);

        $existingMemberIds = $channel->members->pluck('id')->toArray();

        // Filter out existing members
        $newMemberIds = array_diff($validatedData['member_ids'], $existingMemberIds);

        if (empty($newMemberIds)) {
            return response()->json(['message' => 'All members are already in the channel.'], 400);
        }

        // Add the new members to the channel
        $channel->members()->attach($newMemberIds);

        return response()->json(['message' => 'Members added to the channel.']);
    }
}
