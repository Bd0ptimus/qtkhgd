<?php

namespace App\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVerification;
use App\Admin\Admin;
use App\Models\School;

class RedirectSchoolAgency
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $school_id = $request->school_id;
        $currentUrl = $request->fullUrl();
        $school = School::where('id', $school_id)->with('branches')->first();
        
        if(empty($request->school_branch)) {
            if(count($school->branches) > 0) {
                if(count($request->query()) > 0) return redirect("{$currentUrl}&school_branch={$school->branches[0]->id}");
                else return redirect("{$currentUrl}?school_branch={$school->branches[0]->id}");
            } 
            else return redirect()->route('admin.school.view_branch_list', ['id' => $school_id]);
        } 
        else return $next($request);
    }
}
