<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2016/12/09
 * Time: 10:46
 */
namespace App\Utils;
class WxCallbackUtil
{

	private $appId;
	private $appSecret;

	public function __construct()
	{
		parent::__construct();
		$this->appId     = C("APP_ID");
		$this->appSecret = C("APP_SECRET");
	}

	public function getWxCode($redirect_url)
	{
		$redirect_url = urlencode($redirect_url);
		$url          = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appId . "&redirect_uri=" . $redirect_url . "&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
		header("Location:" . $url);
	}

	public function getUserInfoByAccessToken($result)
	{

		$result = json_decode($result);
		session("access_token", $result->access_token);
		session("openid", $result->openid);
		$userinfo_url = 'https://api.weixin.qq.com/sns/userinfo';
		unset($params);
		$params['access_token'] = $result->access_token;
		$params['openid']       = $result->openid;
		$params['lang']         = 'zh_CN';
		$userinfo               = $this->curl($userinfo_url, $params);
		$userinfo               = json_decode($userinfo);
		$unionid                = empty($userinfo->unionid) ? $userinfo->unionid : '';
		$data                   = array(
			'wx_open_id' => $result->openid
		, 'wx_union_id'  => $userinfo->unionid
		, 'buyer_nick'   => $userinfo->nickname
		, 'sex'          => $userinfo->sex
		, 'province'     => $userinfo->province
		, 'city'         => $userinfo->city
		, 'buyer_img'    => $userinfo->headimgurl
		);
		return $data;
	}

	public function getAccessTokenByCode($code)
	{
		$oauth                = 'https://api.weixin.qq.com/sns/oauth2/access_token';
		$params['appid']      = $this->appId;
		$params['secret']     = $this->appSecret;
		$params['code']       = $code;
		$params['grant_type'] = 'authorization_code';
		$result               = $this->curl($oauth, $params);
		return $result;
	}

	public function generateToken($flush = false)
	{
		$urlAccessToken = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
		$token          = session('token');
		if ($token && !$flush) {
			return $token['access_token'];
		}

		//没有获取到,或者要求强制刷新
		$token = $this->curl($urlAccessToken);
		if (!$token) {
			throw new Exception("获取token失败");
		}

		//接口返回: {"access_token":"Nu9jllyxOd7YOlhJsRVxiXuaP-fka927pXBspAtfAsgSkySBmd5SAas1ADy7xcrPmQuvcEnZJSyOm2y4fH_NdDG4hLYQVlehzm-1oWrryEA","expires_in":7200}
		$token = json_decode($token, true);

		$token = session('token', $token); //保存token

		return $token['access_token'];
	}

	public function curl($url, $data = [])
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, []);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_COOKIEJAR, null);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$content = curl_exec($ch);

		return $content;
	}

}