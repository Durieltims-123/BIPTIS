<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {
    DB::table('users')->insert([
      [
        'name' => 'BAC SEC',
        'email' => 'admin@ibits.com',
        'email_verified_at' => now(),
        'password' => Hash::make('secret'),
        'created_at' => now(),
        'updated_at' => now()
      ],

      [
        'name' => 'BAC-SEC',
        'email' => 'bactwg@ibits.com',
        'email_verified_at' => now(),
        'password' => Hash::make('secret'),
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'name' => 'PEO',
        'email' => 'peo@ibits.com',
        'email_verified_at' => now(),
        'password' => Hash::make('secret'),
        'created_at' => now(),
        'updated_at' => now()
      ],
    ]);

    DB::table('roles')->insert([
      [
        'name'=>'BAC-SEC',
        'display_name'=>'BAC-SEC',
        'created_at' => now(),
        'updated_at' => now()
      ],

      [
        'name'=>'BAC-TWG',
        'display_name'=>'BAC-TWG',
        'created_at' => now(),
        'updated_at' => now()
      ],

      [
        'name'=>'PEO',
        'display_name'=>'PEO',
        'created_at' => now(),
        'updated_at' => now()
      ],
    ]);

    DB::table('user_roles')->insert([
      [
        'user_id'=>1,
        'role_id'=>1
      ],
      [
        'user_id'=>2,
        'role_id'=>2
      ],
      [
        'user_id'=>3,
        'role_id'=>3
      ],
    ]);
  }
}
