<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\DB;

use App\User;
use App\StoreUserRequest;
use Hash;

class UserControllerAPI extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page')) {
            return UserResource::collection(User::paginate(5));
        } else {
            return UserResource::collection(User::all());
        }

        /*
            Caso não se pretenda fazer uso de Eloquent API Resources (https://laravel.com/docs/5.5/eloquent-resources), é possível implementar com esta abordagem:
            if ($request->has('page')) {
                return User::with('department')->paginate(5);;
            } else {
                return User::with('department')->get();;
            }
        */
    }

    public function show($id)
    {
        return new UserResource(User::find($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'username' => 'required|string|max:30',
            'email' => 'required|email|unique:users'
        ]);

        $user = new User();
        $user->fill($request->all());
        $user->password = Hash::make(str_random(8));
        $user->save();
        return response()->json(new UserResource($user), 201);
    }

    public function update(Request $request, $id) 
    {
        $request->validate([
            'name' => 'min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'username' => 'string|max:30',
            'email' => 'email|unique:users,email,'.$id,
        ]);

        $user = User::findOrFail($id);
        if ($request->current_password) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Incorrect current password',
                    'status' => 401
                ], 401);
            }

            $user->password = bcrypt($request->password);
        } else {
            $user->fill($request->all());
        }

        $user->save();
        return new UserResource($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function emailAvailable(Request $request)
    {
        $totalEmail = 1;
        if ($request->has('email') && $request->has('id')) {
            $totalEmail = DB::table('users')->where('email', '=', $request->email)->where('id', '<>', $request->id)->count();
        } else if ($request->has('email')) {
            $totalEmail = DB::table('users')->where('email', '=', $request->email)->count();
        }
        return response()->json($totalEmail == 0);
    }

    public function myProfile(Request $request)
    {
        return new UserResource($request->user());
    }
}
