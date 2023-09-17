<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMemberRequest;
use App\Models\Member;
use App\Models\Workspace;

class MemberController extends Controller
{
    function store(CreateMemberRequest $request, Workspace $workspace)
    {
        try {
            $workspaceMember = Member::create([
                'user_id' => 2,   //todo replace with auth id
                'workspace_id' => $workspace->id,
                'name' => $request->name,
                'is_admin' => 1
                //todo adding profile pic will do later
            ]);

            return response()->json(['message' => 'User added to the workspace', 'data' => $workspaceMember], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Adding member failed', 'error' => $e->getMessage()], 500);
        }
    }
}
