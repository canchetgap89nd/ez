<?php

namespace App\models\Admin\ExtractorFacebook;

use Illuminate\Database\Eloquent\Model;

class AdminToken extends Model
{
    protected $fillable = [
    	'access_token_lord', 'admin_id'
    ];

    public $timestamps = false;
}
