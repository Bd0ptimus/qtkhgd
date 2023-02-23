<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSubjectPlan extends Model
{
    protected $table = 'group_subject_plan';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
       'group_plan_id', 'subject_id', 'content'
    ];
}
