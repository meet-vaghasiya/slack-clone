<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMemberRequest;
use App\Models\Member;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    function store(CreateMemberRequest $request, Workspace $workspace)
    {
        $url = null;
        if ($request->hasFile('avatar')) {
            $uploadedFile = $request->file('avatar');
            $url = Storage::put("avatars", $uploadedFile);
        }
        try {
            $workspaceMember = Member::create([
                'user_id' => Auth::id(),
                'workspace_id' => $workspace->id,
                'name' => $request->name,
                'is_admin' => 1,
                'avatar' => $url
                //todo adding profile pic will do later
            ]);

            return response()->json(['message' => 'User added to the workspace', 'data' => $workspaceMember], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Adding member failed', 'error' => $e->getMessage()], 500);
        }
    }
}
