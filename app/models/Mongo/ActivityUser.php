<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ActivityUser extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'activity_users';
    protected $fillable = [
    	'user_id', 'activity_code', 'activity', 'activity_desc', 'from_platform'
    ];
}
