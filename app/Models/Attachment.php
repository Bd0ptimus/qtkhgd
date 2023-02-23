<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['id'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'path',
        'name',
    ];

    /**
     * relatioship business rules:
     */
    public function attachable() {
        return $this->morphTo();
    }
}
