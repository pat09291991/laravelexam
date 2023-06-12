<?php

namespace App\Traits;

use App\Models\Role;
use Carbon\Carbon;
use Error;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

trait UserTraits
{
    private function validateRequest($params, $user = null)
    {
        $validate = [
            'full_name' => 'required|string|max:255',
            'email_address' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:8|max:255|confirmed',
            'roles' => 'required'
        ];

        if ($user) {
            $validate = [
                'full_name' => 'required|string|max:255',
                'email_address' => 'required|string|email|max:255|unique:users,email_address,' . $user->id,
                'password' => 'nullable|min:8|max:255|confirmed',
                'roles' => 'required'
            ];
        }

        $validator = Validator::make($params, $validate);

        if ($validator->fails()) {
            throw new Error($validator->errors()->first());
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
