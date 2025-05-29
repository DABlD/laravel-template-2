<?php

namespace App\Helpers;

use App\Models\Log;

class Helper
{
	public static function log($uid, $action, $tid = null){
		$log = new Log();
		$log->user_id = $uid;
		$log->action = $action;
		$log->target_id = $tid;
		$log->ip = request()->ip();
		$log->save();
	}
}