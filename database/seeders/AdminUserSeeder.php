<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\UserTraits;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    use UserTraits;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'full_name' => 'John Doe',
            'email_address' => 'johndoe@email.com',
            'password' => Hash::make("test1234")
        ];

        $user = User::create($data);

        $roles = [4];
        $this->createUserRoles($roles, $user);
    }
}
