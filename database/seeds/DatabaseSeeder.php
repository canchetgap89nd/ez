<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(GroupCustomerSeeder::class);
        $this->call(RoleAndPermissionSeeder::class);
        $this->call(QuickAnswerSeeder::class);
        $this->call(VietNamLocationSeeder::class);
        $this->call(VietNamWardSeeder::class);
        $this->call(UserAdminSeeder::class);
        $this->call(NamespaceSlugSeeder::class);
    }
}
