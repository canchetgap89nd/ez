<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class CustomerNote extends Model
{
	protected $fillable = [
		'customer_id', 'note_content'
	];
}
