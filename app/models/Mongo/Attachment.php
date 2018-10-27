<?php

namespace App\models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Attachment extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'attachments';
    protected $fillable = [
    	'entity_id', 'url', 'preview_url', 'file_url', 'type', 'entity_type'
    ];
}
