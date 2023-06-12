<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Error;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->query('sortField', 'id');
        $sortOrder = $request->query('sortOrder', 'desc');
        $sizePerPage = $request->query('sizePerPage', 10);
        $search = $request->query('q', '');

        $users = Role::where(function (Builder $query) use ($search) {
            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }
        })->orderBy($sortField, $sortOrder)
            ->paginate($sizePerPage);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:roles'
            ]);

            if ($validator->fails()) {
                throw new Error($validator->errors()->first());
            }

            Role::create(['name' => $request->name]);

            return response()->json("Successfully created a new role");
        } catch (Throwable $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(Role $role)
    {
        return $role;
    }


    public function update(Request $request, Role $role)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id
            ]);

            if ($validator->fails()) {
                throw new Error($validator->errors()->first());
            }

            $role->update(['name' => $request->name]);

            return response()->json("Successfully updated the role");
        } catch (Throwable $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
