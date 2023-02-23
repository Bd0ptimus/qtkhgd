<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegularGroupSubject extends Model
{
    protected $table = 'regular_group_subject';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    protected $fillable = [
        'regular_group_id',
        'subject_id'
    ];
}
