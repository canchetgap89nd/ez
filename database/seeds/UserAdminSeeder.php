<?php

use Illuminate\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_admins')->insert([
        	[
                'email' => 'sieubaochotsale@gmail.com',
    			'password' => Hash::make('chotsalevip9x')
    		]
        ]);
    }
}
