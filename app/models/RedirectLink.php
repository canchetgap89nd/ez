<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class RedirectLink extends Model
{
    protected $fillable = [
    	'from_link', 'to_link'
    ];
}
