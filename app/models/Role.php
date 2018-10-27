<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\Permission;

class Role extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'display_name', 'description'];

    public function users()
    {
    	return $this->belongsToMany('App\User', "role_users", 'role_id', 'user_id');
    }
    
    public function permissions()
    {
    	return $this->belongsToMany("App\models\Permission", "permission_roles", "role_id", "permission_id");
    }
}
