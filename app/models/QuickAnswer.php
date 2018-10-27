<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class QuickAnswer extends Model
{
    protected $table = 'quick_answers';

    protected $fillable = ['user_id', 'quick_text', 'answer_text', 'id', 'staff_id'];
}
