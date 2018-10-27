<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\AccountPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CampaignPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ExportProductPolicy;
use App\Policies\ImportProductPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use Laravel\Passport\Passport;
use App\Extensions\AdminUserProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        // User::class => AccountPolicy::class,
        // Category::class => CategoryPolicy::class,
        // Campaign::class => CampaignPolicy::class,
        // Conversation::class => ConversationPolicy::class,
        // Customer::class => CustomerPolicy::class,
        // ExportProduct::class => ExportProductPolicy::class,
        // ImportProduct::class => ImportProductPolicy::class,
        // Order::class => OrderPolicy::class,
        // Product::class => ProductPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    { 
        $this->registerPolicies();
        Auth::provider('usersAdmin', function ($app, array $config) {
            return new AdminUserProvider();
        });
        Gate::resource('account', 'App\Policies\AccountPolicy');
        Gate::resource('category', 'App\Policies\CategoryPolicy');
        Gate::resource('campaign', 'App\Policies\CampaignPolicy');
        Gate::resource('conversation', 'App\Policies\ConversationPolicy');
        Gate::resource('customer', 'App\Policies\CustomerPolicy');
        Gate::resource('exportProduct', 'App\Policies\ExportProductPolicy');
        Gate::resource('importProduct', 'App\Policies\ImportProductPolicy');
        Gate::resource('order', 'App\Policies\OrderPolicy');
        Gate::resource('product', 'App\Policies\ProductPolicy', [
            'export' => 'exportFile'
        ]);
        Passport::routes();
    }
}
