<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "email_address" => "required|email",
                "password" => "required"
            ]);

            if ($validator->fails()) {
                throw new Error($validator->errors()->first());
            }

            $user = User::where("email_address", $request->email_address)->first();
            if (!$user) {
                throw new Error("Invalid email address or password");
            }

            if (Hash::check($request->password, $user->password)) {
                throw new Error("Invalid email address or password");
            }

            return [
                "token" => $user->createToken($user->email_address)->plainTextToken,
                "expiration_date" => Carbon::now()->addDay()->toDateTimeString(),
                'name' => $user->full_name,
                'roles' => json_encode($user->roles->map(function ($userRole) {
                    return $userRole->role->name;
                })),
            ];
        } catch (Throwable $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json("Logout Successfully.");
    }
}
