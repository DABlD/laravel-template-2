<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Log;

class LogController extends Controller
{
    public function create(Request $req){
        $log = new Log();
        $log->user_id = $req->user_id ?? auth()->user()->id;
        $log->target_id = $req->id;
        $log->action = $req->action;
        $log->ip = $req->ip();
        $log->save();
    }
}