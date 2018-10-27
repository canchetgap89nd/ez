<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class TranferUser extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'tranfer_users';
    protected $fillable = [
    	'conver_count', 'user_id'
    ];
}
