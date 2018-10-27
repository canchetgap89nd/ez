<?php

use Illuminate\Database\Seeder;

class GroupCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_customers')->insert([
        	['user_id' => 0, 'group_name' => 'Đã mua', 'group_color' => '#2ab27b'],
        	['user_id' => 0, 'group_name' => 'Chưa mua', 'group_color' => '#8eb4cb'],
        	['user_id' => 0, 'group_name' => 'Không mua', 'group_color' => '#777777'],
        	['user_id' => 0, 'group_name' => 'Sẽ mua', 'group_color' => '#cbb956'],
        	['user_id' => 0, 'group_name' => 'Khách VIP', 'group_color' => '#e80e0e'],
        ]);
    }
}
