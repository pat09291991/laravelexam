<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Traits\UserTraits;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use UserTraits;

    public function index(Request $request)
    {
        $sortField = $request->query('sortField', 'id');
        $sortOrder = $request->query('sortOrder', 'desc');
        $sizePerPage = $request->query('sizePerPage', 10);
        $rolesFilter = explode(",", $request->query('roles'));
        $search = $request->query('q', '');
        $roles = Role::all();

        $users = User::where(function (Builder $query) use ($rolesFilter, $search) {
            $query->whereHas('roles', function ($q) use ($rolesFilter) {
                $q->whereIn('role_id', $rolesFilter);
            });

            if (!empty($search) && !empty($rolesFilter[0])) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email_address', 'like', "%{$search}%");
            }
        })->orderBy($sortField, $sortOrder)
            ->paginate($sizePerPage);

        $users->getCollection()->transform(function ($user) use ($roles) {
            $transformedUser = $user;
            $userRoles = $user->roles->pluck('role_id');
            $roles = json_encode($roles->whereIn('id', $userRoles)->pluck('name'));
            unset($user->roles);
            unset($user->password);

            $transformedUser->roles = $roles;

            return $transformedUser;
        });

        return response()->json($users);
    }


    public function store(Request $request)
    {
        try {
            $params = $request->all();
            $this->validateRequest($params);
            $this->validateRoles($params['roles']);

            $storeParams = [
                'full_name' => $params['full_name'],
                'email_address' => $params['email_address'],
                'password' => Hash::make($params['password']),
            ];

            $user = User::create($storeParams);
            $this->createUserRoles($params['roles'], $user);

            return response()->json('Successfully create a new user');
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

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

    public function update(Request $request, User $user)
    {
        try {
            $params = $request->all();
            $this->validateRequest($params, $user);
            $this->validateRoles($params['roles']);

            $updateParams = [
                'full_name' => $params['full_name'],
                'email_address' => $params['email_address'],
            ];

            if ($params['password'] && Hash::check($params['password'], $user->password)) {
                $updateParams['password'] = Hash::make($params['password']);
            }

            $user->update($updateParams);
            $user->roles()->delete();
            $this->createUserRoles($params['roles'], $user);

            return response()->json('Successfully update the user');
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
