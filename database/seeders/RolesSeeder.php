<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'Author', 'created_at'=> now(), 'updated_at'=> now()],
            ['name' => 'Editor', 'created_at'=> now(), 'updated_at'=> now()],
            ['name' => 'Subscriber', 'created_at'=> now(), 'updated_at'=> now()],
            ['name' => 'Administrator', 'created_at'=> now(), 'updated_at'=> now()],
        ];
        
        DB::table('roles')->insert($roles);
    }
}
