<?php
namespace App\Controller;
use App\Services\UserService;
use Think\Controller;
class UserController extends BaseController {

	private $userService;

    public function __construct(){
		$this->userService = new UserService();
	}


	public function login()
	{
		$params = $_POST;
		print_r($params);
		echo "-------";
		foreach($params as $a=>$b){
			print_r($b);
			echo "-------";
		}
		print_r($params);exit;

		//$params = I('get.');
		$params = $this->getRequest('wx_open_id');
		print_r($params);exit;
	//	$params = json_decode($params,true);
		if( !$params || empty($params['wx_open_id'])){
			return $this->returnApiError("请求参数错误！");
		}
		$result = $this->userService->userVerify($params);
	}
	public function userToken(){
		$validation = new UserTokenValidation();
		$params = $this->request->getJsonRawBody(true);
		$validation->validate($params);
		$phone = $params['phone'];
		$password = $params['password'];
		$loginType = $params['loginType'];
		$accountService = new AccountService();
		$user = $accountService->login($phone, $password, $loginType);
		$accessToken = SecurityUtil::getAuthToken($user['id'], $user['site_id'], $user['pharmacist_id'],
		$user['store_id'], $user['store_user_id'], $phone, $this->device);
		return [
			'accessToken' => $accessToken,
			'userInfo'    => $user,
		];
	}
}