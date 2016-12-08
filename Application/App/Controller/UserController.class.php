<?php
namespace App\Controller;
use Think\Controller;
class UserController extends BaseController {


	public function login($phone, $password, $loginType = self::LOGIN_TYPE_1, $device = null)
	{
		//这里做微信授权操作
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