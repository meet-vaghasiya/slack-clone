<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMemberRequest;
use App\Mail\InvitationEmail;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

use function Laravel\Prompts\error;

class MemberController extends Controller
{
    function index(Request $request, $workspaceId)
    {
        $members =  Member::select(['id', 'name', 'avatar'])->where('workspace_id', $workspaceId)->get();
        return response()->json(['message' => 'User added to the workspace', 'data' => $members], 200);
    }


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
                'is_admin' => $workspace->user->id == Auth::id(),
                'avatar' => $url
                //todo adding profile pic will do later
            ]);

            return response()->json(['message' => 'User added to the workspace', 'data' => $workspaceMember], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Adding member failed', 'error' => $e->getMessage()], 500);
        }
    }

    function invites(Request $request, $workspaceId)
    {
        $workspace = Workspace::find($workspaceId);
        try {
            $request['workspace_id'] =  $workspaceId;

            $request->validate([
                'emails' => 'required|array|min:1',
                'emails.*' => 'email',
                'workspace_id' => 'required|exists:workspaces,id'
            ]);

            $existingVerifiedUser =  User::whereHas('members', function ($query) use ($workspaceId) {
                $query->where('workspace_id', $workspaceId);
            })->pluck('email')->toArray();

            $filteredEmails = array_diff($request->input('emails'), $existingVerifiedUser);


            foreach ($filteredEmails as $email) {

                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = User::create([
                        'email' => $email
                    ]);
                }
                $token = Str::random(20);
                // todo add expires time also after
                $invitation = Invitation::updateOrCreate(
                    [
                        'workspace_id' => $workspaceId,
                        'email' => $email
                    ],
                    [

                        'token' => $token
                    ]
                );
                Mail::to($email)->send(new InvitationEmail($invitation, $workspace));
            }

            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Adding member failed', 'error' => $e->getMessage()], 500);
        }
    }

    function acceptInvitation(Request $request, $token)
    {
        try {
            $request['token'] =  $token;

            $request->validate([
                'token' => 'required|exists:invitations,token'
            ]);

            $invitation = Invitation::where('token', $token)->first();
            $user = User::where('email', $invitation->email)->first();
            $workspace = Workspace::find($invitation->workspace_id);
            return response()->json(['user' => $user, 'workspace' => $workspace]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Adding member failed', 'error' => $e->getMessage()], 500);
        }
    }
}
