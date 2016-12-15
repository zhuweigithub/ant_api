<?php
namespace App\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function index(){
		echo 123;
       return 111;
    }
	public function test(){
		//$auth_token = new \Model\Dto\AuthToken();
		$user = D('Users');
		$result = $user->find();
		print_r($result->buyer_id);
		fb($result);
	}
}