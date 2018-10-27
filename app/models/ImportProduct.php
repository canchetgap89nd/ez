<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ImportProduct extends Model
{
    public function products()
    {
    	return $this->hasMany('App\models\ProductsImport', 'import_id');
    }

    public function userCancel()
    {
    	return $this->belongsTo('App\User', 'user_id_cancel');
    }
}
