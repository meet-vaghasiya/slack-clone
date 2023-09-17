<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWorkspaceRequest;
use App\Models\Workspace;

class WorkspaceController extends Controller
{
    public function store(CreateWorkspaceRequest $request)
    {
        try {
            $workspace = Workspace::create([
                'name' => $request->input('name'),
                'user_id' => 2  // todo: replace with Auth::id()
            ]);

            // Return a response indicating success
            return response()->json(['message' => 'Workspace created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Workspace creation failed', 'error' => $e->getMessage()], 500);
        }
    }
}
