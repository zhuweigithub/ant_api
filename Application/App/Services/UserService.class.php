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
	private $_userDb;

	public function __construct()
	{
		$this->_userDb = D("Users");
	}

	public function userVerify($wx_open_id)
	{
		$result = $this->_userDb ->field('mobile')->select();
		fb($result);
	}
}

