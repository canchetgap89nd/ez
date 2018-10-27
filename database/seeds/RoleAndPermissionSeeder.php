<?php

use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
        	['name' => 'ADMINSTRATOR', 'display_name' => 'admin', 'type' => 'GENERAL'],
        	['name' => 'MANAGER', 'display_name' => 'Kiểm soát viên', 'type' => 'GENERAL'],
        	['name' => 'SALER', 'display_name' => 'Kinh doanh', 'type' => 'GENERAL'],
        	['name' => 'STORAGER', 'display_name' => 'Quản lý kho', 'type' => 'GENERAL']
        ]);

        DB::table('permissions')->insert([
            ['name' => 'STAFF_MANAGER'],
            ['name' => 'READ_STAFF'],
            ['name' => 'READ_SETTING'],
            ['name' => 'EDIT_SETTING'],
            ['name' => 'CREATE_SETTING'],
            ['name' => 'CREATE_CONVERSATIONS'],
            ['name' => 'EDIT_CONVERSATIONS'],
            ['name' => 'READ_CONVERSATIONS'],
            ['name' => 'CREATE_CUSTOMERS'],
            ['name' => 'EDIT_CUSTOMERS'],
            ['name' => 'READ_CUSTOMERS'],
            ['name' => 'CUSTOMERS_EXPORT_FILE'],
            ['name' => 'CUSTOMERS_IMPORT_FILE'],
            ['name' => 'CREATE_ORDERS'],
            ['name' => 'EDIT_ORDERS'],
            ['name' => 'READ_ORDERS'],
            ['name' => 'CREATE_CATE'],
            ['name' => 'UPDATE_CATE'],
            ['name' => 'READ_CATE'],
            ['name' => 'CREATE_PRODUCT'],
            ['name' => 'UPDATE_PRODUCT'],
            ['name' => 'READ_PRODUCT'],
            ['name' => 'EXPORT_PRODUCT'],
            ['name' => 'IMPORT_PRODUCT'],
            ['name' => 'READ_CAMPAIGN'],
            ['name' => 'EDIT_CAMPAIGN'],
            ['name' => 'CREATE_CAMPAIGN']
        ]);

        DB::table('permission_roles')->insert([
            ['role_id' => 1, 'permission_id' => 1],
            ['role_id' => 1, 'permission_id' => 2],
            ['role_id' => 1, 'permission_id' => 3],
            ['role_id' => 1, 'permission_id' => 4],
            ['role_id' => 1, 'permission_id' => 5],
            ['role_id' => 1, 'permission_id' => 6],
            ['role_id' => 1, 'permission_id' => 7],
            ['role_id' => 1, 'permission_id' => 8],
            ['role_id' => 1, 'permission_id' => 9],
            ['role_id' => 1, 'permission_id' => 10],
            ['role_id' => 1, 'permission_id' => 11],
            ['role_id' => 1, 'permission_id' => 12],
            ['role_id' => 1, 'permission_id' => 13],
            ['role_id' => 1, 'permission_id' => 14],
            ['role_id' => 1, 'permission_id' => 15],
            ['role_id' => 1, 'permission_id' => 16],
            ['role_id' => 1, 'permission_id' => 17],
            ['role_id' => 1, 'permission_id' => 18],
            ['role_id' => 1, 'permission_id' => 19],
            ['role_id' => 1, 'permission_id' => 20],
            ['role_id' => 1, 'permission_id' => 21],
            ['role_id' => 1, 'permission_id' => 22],
            ['role_id' => 1, 'permission_id' => 23],
            ['role_id' => 1, 'permission_id' => 24],
            ['role_id' => 1, 'permission_id' => 25],
            ['role_id' => 1, 'permission_id' => 26],
            ['role_id' => 1, 'permission_id' => 27],

            ['role_id' => 2, 'permission_id' => 2],
            ['role_id' => 2, 'permission_id' => 3],
            ['role_id' => 2, 'permission_id' => 4],
            ['role_id' => 2, 'permission_id' => 5],
            ['role_id' => 2, 'permission_id' => 6],
            ['role_id' => 2, 'permission_id' => 7],
            ['role_id' => 2, 'permission_id' => 8],
            ['role_id' => 2, 'permission_id' => 9],
            ['role_id' => 2, 'permission_id' => 10],
            ['role_id' => 2, 'permission_id' => 11],
            ['role_id' => 2, 'permission_id' => 12],
            ['role_id' => 2, 'permission_id' => 13],
            ['role_id' => 2, 'permission_id' => 14],
            ['role_id' => 2, 'permission_id' => 15],
            ['role_id' => 2, 'permission_id' => 16],
            ['role_id' => 2, 'permission_id' => 17],
            ['role_id' => 2, 'permission_id' => 18],
            ['role_id' => 2, 'permission_id' => 19],
            ['role_id' => 2, 'permission_id' => 20],
            ['role_id' => 2, 'permission_id' => 21],
            ['role_id' => 2, 'permission_id' => 22],
            ['role_id' => 2, 'permission_id' => 23],
            ['role_id' => 2, 'permission_id' => 24],
            ['role_id' => 2, 'permission_id' => 25],
            ['role_id' => 2, 'permission_id' => 26],
            ['role_id' => 2, 'permission_id' => 27],

            ['role_id' => 3, 'permission_id' => 6],
            ['role_id' => 3, 'permission_id' => 7],
            ['role_id' => 3, 'permission_id' => 8],
            ['role_id' => 3, 'permission_id' => 9],
            ['role_id' => 3, 'permission_id' => 10],
            ['role_id' => 3, 'permission_id' => 11],
            ['role_id' => 3, 'permission_id' => 14],
            ['role_id' => 3, 'permission_id' => 15],
            ['role_id' => 3, 'permission_id' => 16],
            ['role_id' => 3, 'permission_id' => 25],

            ['role_id' => 4, 'permission_id' => 9],
            ['role_id' => 4, 'permission_id' => 10],
            ['role_id' => 4, 'permission_id' => 11],
            ['role_id' => 4, 'permission_id' => 17],
            ['role_id' => 4, 'permission_id' => 18],
            ['role_id' => 4, 'permission_id' => 19],
            ['role_id' => 4, 'permission_id' => 20],
            ['role_id' => 4, 'permission_id' => 21],
            ['role_id' => 4, 'permission_id' => 22],
            ['role_id' => 4, 'permission_id' => 23],
            ['role_id' => 4, 'permission_id' => 24],
            ['role_id' => 4, 'permission_id' => 25],
            ['role_id' => 4, 'permission_id' => 26],
            ['role_id' => 4, 'permission_id' => 27]
        ]);
    }
}
