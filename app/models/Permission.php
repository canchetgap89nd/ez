<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'display_name', 'description'];

    public $timestamps = false;

    public function roles()
    {
    	return $this->belongsToMany("App\models\Role", "permission_roles", "permission_id", "role_id");
    }
}
