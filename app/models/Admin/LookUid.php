<?php

namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class LookUid extends Model
{
    protected $fillable = [
    	'uid', 'look_id'
    ];

    public $timestamps = false;
}