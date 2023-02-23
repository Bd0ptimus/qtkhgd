<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
class YearScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if(request()->route() != null) {
            $routeName = (request()->route()->getName());
            $ignoreRoute = ['student_health_profile', 'staff_health_abnormals'];
            $year = session()->get('year');
            if(empty($year)) {
                $currentMonth = date('m', time());
                $currentYear = date('Y', time());
                if ($currentMonth >= 8) {
                    $year = $currentYear;
                } else {
                    $year = $currentYear - 1;
                }
            }
            $startTime = "$year-08-01 00:00:00"; $endTime = ($year+1)."-06-30 23:59:59";
            
            if(!in_array($routeName, $ignoreRoute)) {
                if(
                    $model instanceof \App\Models\HealthAbnormal
                    || $model instanceof \App\Models\StudentSpecialistTest
                    || $model instanceof \App\Models\StaffSpecialistTest
                ){
                    $builder->where($model->table.'.date', '>=', $startTime)->where($model->table.'.date', '<=', $endTime);
                } else if($model instanceof \App\Models\StudentHealthIndex) {
                    $builder->where($model->table.'.month', '>=', $startTime)->where($model->table.'.month', '<=', $endTime);
                } else if($model instanceof \App\Models\Student) {
                    $builder->where($model->table.'.created_at', '<=', $endTime);
                } else {
                    $builder->where($model->table.'.created_at', '>=', $startTime)->where($model->table.'.created_at', '<=', $endTime);
                }
            }
        }
    }
}