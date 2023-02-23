<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $guarded = ['id'];

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'email',
        'subject',
        'body',
        'attachment'
    ];
}
