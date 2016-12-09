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
		$params = $this->checkDataGet('wx_open_id');
		$result = $this->userService->userVerify($params['wx_open_id']);


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