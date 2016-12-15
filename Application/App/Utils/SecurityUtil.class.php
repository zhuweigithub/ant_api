<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2016/12/14
 * Time: 15:36
 */
namespace App\Utils;
class SecurityUtil
{
	/** 密码加密
	 * @param $str
	 * @return string
	 */
	public static function hash($str)
	{
		return md5($str . "ybzf");
	}

	public static function getAuthToken($buyer_id, $wx_open_id)
	{
		$authToken['buyer_id'] = $buyer_id;
		$authToken['wx_open_id'] = $wx_open_id;
		$authToken['time'] = time();
		return base64_encode(json_encode($authToken));
	}
}