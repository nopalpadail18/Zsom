<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Models\Groups;
use App\Http\Requests\StoreGroupsRequest;
use App\Http\Requests\UpdateGroupsRequest;
use App\Http\Resources\GroupResource;
use App\Models\GroupUsers;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupsRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $group = Groups::create($data);

        $groupUserData = [
            'status' => GroupUserStatus::APPROVED->value,
            'role'   => GroupUserRole::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id(),
        ];

        GroupUsers::create($groupUserData);
        $group->status = $groupUserData['status'];
        $group->role = $groupUserData['role'];

        return response(new GroupResource($group), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupsRequest $request, Groups $groups)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groups $groups)
    {
        //
    }
}
