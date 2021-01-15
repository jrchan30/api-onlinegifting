<?php

namespace App\Http\Controllers;

use App\Events\WebsocketDemoEvent;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.admin', ['except' => ['me', 'update']]);
    }

    public function index(Request $request)
    {
        $s = $request->get('search') ?? '';
        $orderBy = $request->get('orderBy') ?? 'created_at';
        $orderDir = $request->get('orderDir') ?? 'desc';
        $search = '%' . $s . '%';
        $users = User::whereHas('userDetail', function ($query) {
            return $query->where('type', '!=', 'admin');
        })->where('name', 'LIKE', $search)->orderBy($orderBy, $orderDir);
        return UserResource::collection($users->paginate(12));
    }

    public function me()
    {
        $user = auth()->user();
        WebsocketDemoEvent::dispatch("test123");
        return new UserResource($user);
    }

    public function admins(Request $request)
    {
        $s = $request->get('search') ?? '';
        $orderBy = $request->get('orderBy') ?? 'created_at';
        $orderDir = $request->get('orderDir') ?? 'desc';
        $search = '%' . $s . '%';
        $admins = User::whereHas('userDetail', function ($query) {
            return $query->where('type', '=', 'admin');
        })->where('name', 'LIKE', $search)->orderBy($orderBy, $orderDir);
        return UserResource::collection($admins->paginate(12));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->id == $id) {
            $data = $request->validate([
                'email' => 'sometimes|email',
                'password' => 'sometimes',
                'name' => 'sometimes|string',
            ]);

            $details = $request->validate([
                'province' => 'sometimes|string',
                'city' => 'sometimes|string',
                'phone_num' => 'sometimes|string',
                'address' => 'sometimes|string',
            ]);

            if ($request->has('password')) {
                $data['password'] = bcrypt($data['password']);
            }

            // dd($data);
            $user = User::where('id', auth()->user()->id)->first();
            $user->update($data);
            $user->userDetail->update($details);
            return new UserResource($user);
        }
    }

    public function destroy($id)
    {
        $uid = Auth::user()->id;
        $user = User::find($uid);

        if ($user->id === $id) {
            $user->delete();
            return response()->json('Successfully Deleted', 204);
        } else {
            return response()->json(['error' => 'Forbidden not yours'], 403);
        }
    }
}
