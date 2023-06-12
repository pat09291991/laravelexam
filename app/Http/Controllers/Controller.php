<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function filters()
    {
        $roles = Role::select('id', 'name')->get()
            ->map(function ($role) {
                return [
                    'value' => $role->id,
                    'label' => $role->name
                ];
            });

        return response()->json([
            'roles' => $roles
        ]);
    }
}
