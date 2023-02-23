<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictSpecialistSchool extends Model
{
    protected $table = 'district_specialist_school';

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $guarded = ['id'];

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'specialist_id',
        'school_id'
    ];
}
