<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        $totalStudent = 0;
        $totalStaff = 0;
        $schools = collect();
        $now = Carbon::now();

        return view('admin.home', [
            'title' => trans('admin.dashboard'),
            'totalStaff' => $totalStaff,
            'totalStudent' => $totalStudent
        ]);
    }

    public function deny()
    {
        $data = [
            'title' => trans('admin.deny'),
            'icon' => '',
            'method' => session('method'),
            'url' => session('url'),
        ];
        return view('admin.deny', $data);
    }

    public function allRoutes()
    {
        $routes = app()->routes->getRoutes();
        // dd($routes);
        echo "<table style='width:100%'>";
        echo "<tr>";
        echo "<td width='10%'><h4>PREFIX</h4></td>";
        echo "<td width='10%'><h4>URI</h4></td>";
        echo "<td width='10%'><h4>METHOD</h4></td>";
        echo "<td width='10%'><h4>NAME</h4></td>";
        echo "<td width='70%'><h4>middleware</h4></td>";
        echo "<td width='70%'><h4>controller</h4></td>";
        echo "</tr>";
        foreach ($routes as $value) {
            echo "<tr>";
            echo "<td>" . $value->getPrefix() . "</td>";
            echo "<td>" . $value->uri . "</td>";
            echo "<td>" . json_encode($value->methods) . "</td>";
            echo "<td>" . $value->getName() . "</td>";
            echo "<td>" . json_encode($value->getAction('middleware')) . "</td>";
            echo "<td>" . json_encode($value->getAction('controller')) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function changeYear($year) {
        session()->put('year', $year);
        return redirect()->back();
    }

    public function selectModule(Request $request){
        if(!empty($request->select_module)) {
            session()->put('workingModule', $request->select_module);
            return redirect()->route('admin.home');
        }
        /* foreach(SYSTEM_MODELS as $module) {
            echo "<a href='?select_module={$module['value']}'>{$module['name']}</a><br/>";
        } */

        return view('admin.select_module');
    }
}
