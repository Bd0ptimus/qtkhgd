<?php

namespace App\Admin\Models;

use App\Models\Base\BaseModel as Model;

class AdminLog extends Model
{
    protected $guarded = [];
    public $table = 'admin_log';
    public static $methodColors = [
        'GET' => 'green',
        'POST' => 'yellow',
        'PUT' => 'blue',
        'DELETE' => 'red',
    ];

    public static $methods = [
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH',
        'LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE',
    ];

    /**
     * Log belongs to users.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(AdminUser::class);
    }
}
