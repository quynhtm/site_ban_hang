<?php
/*
* @Created by: Quynhtm
* @Author    : quynhtm
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Library\PHPDev\Utility;

class DashBoardController extends BaseAdminController{

    public function __construct(){
		parent::__construct();
	}

	public function listView(){
		$messages = Utility::messages('messages');
		return view('admin.dashboard.list',['messages'=>$messages]);
	}
}