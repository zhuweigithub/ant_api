<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2016/12/09
 * Time: 11:23
 */
namespace App\Services;
use App\Utils\SecurityUtil;

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

	/**
	 * @param $wx_open_id
	 * @param $wx_union_id
	 * @param $nickname
	 * @param $sex
	 * @param $province
	 * @param $city
	 * @param $headimgurl
	 * @return array
	 */
	public function userVerify($wx_open_id, $wx_union_id, $nickname, $sex, $province, $city, $headimgurl)
	{
		$map['wx_open_id'] = $wx_open_id;
		$result            = $this->_userDb->field('buyer_id,wx_open_id,status')->where($map)->find();
		$buyer_id = "";
		if (count($result) > 0) {
			$buyer_id = $result['buyer_id'];
			if ($result['status'] == self::USER_STATUS_BLACKLIST) {
				return array('statusCode' => -1, 'errMsg' => "用户被加入黑名单");
			} elseif ($result['status'] == self::USER_STATUS_FROST) {
				return array('statusCode' => -1, 'errMsg' => "用户被冻结");
			}
		}
		//注册更新流程
		$userId = $this->registerUser($buyer_id,$wx_open_id, $wx_union_id, $nickname, $sex, $province, $city, $headimgurl);
		//todo 生成token 未完成
		$AuthToken = $this->getAuthToken($userId,$wx_open_id);
		$token = array(
			"AuthToken" => $AuthToken
		);
		return array('statusCode' => 0, 'AuthToken' => $token);
	}

	/**
	 * @param $buyer_id
	 * @param $wx_open_id
	 * @param $wx_union_id
	 * @param $nickname
	 * @param $sex
	 * @param $province
	 * @param $city
	 * @param $headimgurl
	 * @return int|mixed
	 */
	public function registerUser($buyer_id,$wx_open_id, $wx_union_id, $nickname, $sex, $province, $city, $headimgurl)
	{
		$id = 0;
		$data = array(
		  'wx_open_id'   => $wx_open_id
		, 'wx_union_id'  => $wx_union_id
		, 'buyer_nick'   => $nickname
		, 'sex'           => $sex
		, 'province'     => $province
		, 'city'          => $city
		, 'buyer_img'    => $headimgurl
		);
		if( $buyer_id > 0){
			$data['buyer_id'] = $buyer_id;
			$this->_userDb->save($data);
			$id = $buyer_id;
		}else{
			$id = $this->_userDb->add($data);
		}
		return $id;

	}

	/** 获取token
	 * @param $userId
	 * @param $wx_open_id
	 * @return string
	 */
	private function getAuthToken($userId,$wx_open_id){
		$security = new SecurityUtil();
		return $security->getAuthToken($userId,$wx_open_id);
	}
}

