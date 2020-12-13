<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserDetailResource;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Auth::user()->userDetail;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => 'sometimes|string',
            'phone_num' => 'required|string'
        ]);

        $userId = auth('sanctum')->user()->id;
        $request['type'] = 'customer';
        $detail = UserDetail::firstOrCreate(
            ['user_id' => $userId],
            $request->all()
        );

        return new UserDetailResource($detail);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserDetail  $userDetail
     * @return \Illuminate\Http\Response
     */
    public function show(UserDetail $userDetail)
    {
        $user = auth('sanctum')->user();

        if ($userDetail->user_id == $user->id) {
            return new UserDetailResource($userDetail);
        }

        return response()->json(['Error' => 'Forbidden'], 403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserDetail  $userDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserDetail $userDetail)
    {
        $validated = $request->validate([
            'address' => 'sometimes|string',
            'phone_num' => 'sometimes|string'
        ]);

        $user = auth('sanctum')->user()->id;
        if ($userDetail->user_id == $user) {
            $userDetail->update($validated);
            return new UserDetailResource($userDetail);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserDetail  $userDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserDetail $userDetail)
    {
        //
    }
}
