<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\models\Page;
use App\models\UserQuickAnswer;
use App\models\QuickAnswer;
use App\models\ExportProduct;
use App\models\CustomerReport;
use App\models\GroupCustomer;
use App\models\Campaign;
use App\models\Category;
use App\models\Property;
use App\models\PropertyValue;
use App\models\Customer;
use App\models\SettingBasic;
use App\models\Product;
use App\models\Order;
use App\models\TempRole;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use App\models\Admin\PackageAndPayment\UserPackage;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'user_fb_id', 'user_access_token', 'user_phone', 'user_email', 'parent_user_id', 'username', 'name', 'user_fb_email', 'user_time_expire', 'blocked', 'destroyed', 'time_expire_blocked', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token', 'api_token', 'username'
    ];

    public function roles()
    {
        return $this->belongsToMany("App\models\Role", "role_users", "user_id", "role_id");
    }

    public function device()
    {
        return $this->hasMany('App\models\DeviceInfo', 'user_id');
    }

    public function checkHasRole($roleCheck)
    {
        $roles = $this->roles()->get();
        foreach ($roles as $role) {
            if ($roleCheck == $role->name) {
                return true;
            }
        }
        return false;
    }

    public function hasRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->checkHasRole($role)) {
                    return true;
                }
            }
        } else {
            return $this->checkHasRole($roles);
        }
        return false;
    }

    public function checkHasPerm($perm)
    {
        $perms = $this->roles()->permissions()->get();
        foreach ($perms as $perm) {
            if ($perm->name == $perm) {
                return true;
            }
        }
        return false;
    }

    public function hasPerms($perms)
    {
        if (is_array($perms)) {
            foreach ($perms as $perm) {
                if ($this->checkHasPerm($perm)) {
                    return true;
                }
            }
        } else {
            return $this->checkHasPerm($perms);
        }
        return false;
    }

    public function isAdmin()
    {
        return $this->hasRole("ADMINSTRATOR");
    }

    public function isManager()
    {
        return $this->hasRole("MANAGER");
    }

    public function isSaler()
    {
        return $this->hasRole("SALER");
    }

    public function isWare()
    {
        return $this->hasRole("STORAGER");
    }

    public function accessToken()
    {
        return $this->user_access_token;
    }

    public function categories()
    {
        return $this->hasMany('App\models\Category', 'user_id');
    }

    public function catesUser()
    {
        return Category::where('user_id', $this->adminId());
    }

    /**
     * get all product properties
     * @return [type]
     */
    public function properties()
    {
        return $this->hasMany('App\models\Property', 'user_id');
    }

    public function propUser()
    {
        return Property::where('user_id', $this->adminId());
    }

    public function propertiesValue()
    {
        return PropertyValue::where('user_id', $this->adminId());
    }

    public function ordersUser()
    {
        if ($this->isAdmin() || $this->isManager()) {
            return Order::where('user_id', $this->adminId());
        }
        return Order::where('user_id', $this->adminId())->where('staff_id', $this->id);
    }
    
    public function orders()
    {
        return  DB::table('orders')
                    ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                    ->leftJoin('viet_nam_wards', 'orders.ward_id', '=', 'viet_nam_wards.id')
                    ->leftJoin('viet_nam_districts', 'orders.district_id', '=', 'viet_nam_districts.id')
                    ->leftJoin('viet_nam_provinces', 'orders.province_id', '=', 'viet_nam_provinces.id')
                    ->select('users.name', 'orders.*', 'viet_nam_wards.name_ward', 'viet_nam_districts.name_district', 'viet_nam_provinces.name as name_province')
                    ->where('orders.user_id', $this->adminId());
    }

    public function transports()
    {
        return  DB::table('transports')
                    ->leftJoin('orders', 'orders.id', '=', 'transports.order_id')
                    ->leftJoin('viet_nam_provinces', 'orders.province_id', '=', 'viet_nam_provinces.id')
                    ->leftJoin('viet_nam_districts', 'orders.district_id', '=', 'viet_nam_districts.id')
                    ->leftJoin('viet_nam_wards', 'orders.ward_id', '=', 'viet_nam_wards.id')
                    ->select('transports.*', 'orders.order_code', 'orders.created_at as time_create_order', 'orders.total_amount', 'orders.status_order', 'orders.ad_receive', 'viet_nam_provinces.name', 'viet_nam_districts.name_district', 'viet_nam_wards.name_ward')
                    ->where('orders.user_id', $this->adminId());
    }

    public function imports()
    {
        return  DB::table('import_products')
                    ->leftJoin('users', 'users.id', '=', 'import_products.user_id')
                    ->select('import_products.*', 'users.name as username_import')
                    ->where('import_products.user_id', $this->adminId());
    }

    public function importsUser()
    {
        return DB::table('import_products')
                    ->where('user_id', $this->adminId());
    }

    /**
     * list export product
     * @return 
     */
    public function exports() 
    {
        return  DB::table('export_products')
                    ->leftJoin('users', 'users.id', '=', 'export_products.user_id')
                    ->select('export_products.*', 'users.name as user_ex')
                    ->where('export_products.user_id', $this->adminId());
    }

    public function exportsUser()
    {
        return DB::table('export_products')
                    ->where('user_id', $this->adminId());
    }

    public function customersList()
    {
        return DB::table('customers')
                    ->leftJoin('customer_and_groups', 'customer_and_groups.customer_id', '=', 'customers.id')
                    ->leftJoin('group_customers', 'group_customers.id', '=', 'customer_and_groups.group_id')
                    ->select('customers.*', 'customer_and_groups.group_id', 'group_customers.group_name')
                    ->where('customers.user_id', $this->adminId());
    }

    public function groupsCustomer()
    {
        return GroupCustomer::where('user_id', $this->adminId())
                            ->orWhere('user_id', 0);
    }

    public function pages() {
        return $this->hasMany("App\models\Page", "user_id");
    }

    public function pagesUser() 
    {
        return Page::where('user_id', $this->adminId());
    }

    public function customers() 
    {
        return $this->hasMany('App\models\Customer', 'user_id');
    }

    public function customersUser()
    {
        return Customer::where('user_id', $this->adminId());
    }

    public function accounts()
    {
        return $this->hasMany('App\User', 'parent_user_id');
    }

    public function cusGroups()
    {
        return $this->hasMany('App\models\GroupCustomer', 'user_id');
    }

    public function quickAnswers()
    {
        return QuickAnswer::where('user_id', $this->adminId())
                            ->orWhere('user_id', 0);
    }

    public function productsUser()
    {
        return DB::table('products')
                    ->leftJoin('cate_products', 'cate_products.product_id', '=', 'products.id')
                    ->leftJoin('categories', 'categories.id', '=', 'cate_products.cate_id')
                    ->select('products.*', 'categories.cate_name', 'categories.id as cate_id')
                    ->where('products.user_id', $this->adminId());
    }

    public function productUsers()
    {
        return Product::where('user_id', $this->adminId());
    }

    public function products()
    {
        return $this->hasMany('App\models\Product', 'user_id');
    }

    public function productsAndCamp()
    {
        return DB::table('products')
                    ->leftJoin('campaign_products', 'products.id', '=', 'campaign_products.prod_id')
                    ->leftJoin('campaigns', 'campaigns.id', '=', 'campaign_products.camp_id')
                    ->select('products.*', 'campaigns.id as camp_id', 'campaigns.camp_name', 'campaigns.perc_disc', 'campaigns.sold_out', 'campaigns.start_time', 'campaigns.end_time', 'campaigns.status as camp_status')
                    ->where('products.user_id', $this->adminId());
    }

    public function campaigns()
    {
        return $this->hasMany('App\models\Campaign', 'user_id');
    }

    public function campaignsUser()
    {
        return Campaign::where('user_id', $this->adminId());
    }

    public function generalSetting()
    {
        return SettingBasic::where('user_id', $this->adminId());
    }

    public function blackList()
    {
        return CustomerReport::where('user_id', $this->adminId());
    }

    public function parent()
    {
        return $this->belongsTo("App\User", "parent_user_id");
    }

    /**
     * get id of user if user is admin
     * or get parent user id if user not admin
     * @return [type] [id of user or parent user]
     */
    public function adminId()
    {
        if ($this->isAdmin()) {
            return $this->id;
        }
        return $this->parent_user_id;
    }

    public function hasInvite()
    {
        $temp = TempRole::where('fb_user_staff', $this->user_fb_id)->count();
        if ($temp > 0) {
            return true;
        }
        return false;
    }

    /**
     * check package account
     */
    public function hasPack($packs)
    {
        if (is_array($packs)) {
            foreach ($packs as $pack) {
                if ($this->checkHasPack($pack)) {
                    return true;
                }
            }
        } else {
            if ($this->checkHasPack($packs)) {
                return true;
            }
        }
        return false;
    }

    public function checkHasPack($packName)
    {
        $packs = $this->packages()->get();
        foreach ($packs as $pack) {
            if ($pack->name === $packName && strtotime($pack->pivot->expire_at) > time()) {
                return true;
            }
        }
        return false;
    }

    public function packages()
    {
        return $this->belongsToMany('App\models\Admin\PackageAndPayment\Package', 'user_packages', 'user_id', 'package_id')->withPivot('expire_at');
    }

    public function packagesActive()
    {
        $packages = $this->packages()->get();
        $index = null;
        foreach ($packages as $key => $pack) {
            if ($index === null) {
                if (strtotime($pack->pivot->expire_at) >= time()) {
                    $index = $key;
                }
            } else {
                if ($pack->page_limit > $packages[$index]->page_limit && strtotime($pack->pivot->expire_at) >= time()) {
                    $index = $key;
                }
            }
        }
        return isset($packages[$index]) ? $packages[$index] : null;
    }
}
