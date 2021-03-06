<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index(){
        return $this->_view('dashboard', [
            'title'         => 'Dashboard'
        ]);
    }

    private function _view($view, $data = array()){
        return view($view, $data);
    }
}
