<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailQueue extends Model
{
    protected $table = "email_queues";

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $guarded = ['id'];

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'to',
        'from_email',
        'from_name',
        'subject',
        'message',
        'type',
        'attachments',
        'resourcetype',
        'resourceid',
        'pdf_resource_type',
        'pdf_resource_id',
        'status',
        'started_at'
    ];
}
