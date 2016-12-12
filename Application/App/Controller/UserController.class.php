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
		/*$data                   = array(
			'wx_open_id' => $result->openid
		, 'wx_union_id'  => $userinfo->unionid
		, 'buyer_nick'   => $userinfo->nickname
		, 'sex'          => $userinfo->sex
		, 'province'     => $userinfo->province
		, 'city'         => $userinfo->city
		, 'buyer_img'    => $userinfo->headimgurl
		);*/
		$params = I('get.');
		fb($params);
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