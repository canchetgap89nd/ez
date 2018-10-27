<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class InfoMarketing extends Model
{
    protected $fillable = ['user_id', 'page_id', 'from_conver_id', 'from_entity_id', 'email_mar', 'phone_mar'];
}
