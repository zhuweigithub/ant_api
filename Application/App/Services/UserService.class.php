<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2016/12/09
 * Time: 11:23
 */
namespace App\Services;


class UserService
{
	const USER_STATUS_BLACKLIST = -9; //黑名单
	const USER_STATUS_FROST = -1; //冻结
	const USER_STATUS_NORMAL = 0; //正常
	private $_userDb;

	public function __construct()
	{
		$this->_userDb = D("Users");
	}

	public function userVerify($params)
	{
		$map['wx_open_id'] = $params['wx_open_id'];
		$result            = $this->_userDb->field('buyer_id,wx_open_id,status')->where($map)->find();
		fb($result);
		if (count($result) > 0) {
			if ($result['status'] == self::USER_STATUS_BLACKLIST) {
				return "用户被加入黑名单";
			} elseif ($result['status'] == self::USER_STATUS_FROST) {
				return "用户被冻结";
			}
		} else {
			//注册流程
			$this->registerUser($params);
		}
	}

	public function registerUser($params)
	{
		/**
		 * 		$unionid                = empty($userinfo->unionid) ? $userinfo->unionid : '';
		$data                   = array(
		'wx_open_id' => $result->openid
		, 'wx_union_id'  => $userinfo->unionid
		, 'buyer_nick'   => $userinfo->nickname
		, 'sex'          => $userinfo->sex
		, 'province'     => $userinfo->province
		, 'city'         => $userinfo->city
		, 'buyer_img'    => $userinfo->headimgurl
		);
		 */
		$data                   = array(
		  'wx_open_id' => $params['wx_open_id']
		, 'wx_union_id'  => $params->unionid
		, 'buyer_nick'   => $userinfo->nickname
		, 'sex'          => $userinfo->sex
		, 'province'     => $userinfo->province
		, 'city'         => $userinfo->city
		, 'buyer_img'    => $userinfo->headimgurl
		);

	}
}

