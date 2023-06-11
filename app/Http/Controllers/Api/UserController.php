<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $params = $request->all();
            $validator = Validator::make($params, [
                'full_name' => 'required',
                'email' => 'required|string|email|max:255|unique:users',
                'roles' => 'required'
            ]);

            if ($validator->fails()) {
                throw new Error($validator->errors()->first());
            }

            $this->validateRoles($params['roles']);

            $roles = $params['roles'];
            unset($params['roles']);

            $user = User::create($params);
            $this->createUserRoles($roles, $user);

            return response()->json('Successfully create new user');
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $roles = Role::all();
        $roles = $user->roles->map(function ($userRole) use ($roles) {
            $role = $roles->where('id', $userRole->role_id)->first();
            return [
                'value' => $role->id,
                'label' => $role->name,
            ];
        });

        unset($user->roles);
        $user->roles = $roles;

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        try {
            $params = $request->all();
            $validator = Validator::make($params, [
                'full_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'roles' => 'required'
            ]);

            if ($validator->fails()) {
                throw new Error($validator->errors()->first());
            }

            $this->validateRoles($params['roles']);

            $roles = $params['roles'];
            unset($params['roles']);

            $user->update($params);
            $user->roles()->delete();
            $this->createUserRoles($roles, $user);

            return response()->json('Successfully create new user');
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function validateRoles($requestRoles)
    {
        $roles = Role::whereIn('id', $requestRoles)->count();

        if (count($requestRoles) != $roles) {
            throw new Error('Invalid roles');
        }
    }

    private function createUserRoles($roles, $user)
    {
        $userRoles = [];
        foreach ($roles as $role) {
            $userRoles[] = [
                'user_id' => $user->id,
                'role_id' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('user_roles')->insert($userRoles);
    }
}
