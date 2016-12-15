<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2016/11/52
 * Time: 11:52
 */
namespace App\Controller;

use Think\Controller;

class BaseController extends Controller
{

	protected $userId;
	protected $access_token;
	protected $phone;
	protected $wx_open_id;
	private $_postValue = null;

	const AUTH_TOKEN = 'AuthToken';

	public function __construct()
	{
		//user控制器用于用户验证，返回token，所以无需校验
		if (strpos(get_class($this), 'UserController')) {
			return;
		}

		$accessToken = $_GET[self::AUTH_TOKEN];
		if (!$accessToken) {
			$param = $this->getRequest();
			if (!empty($param[self::AUTH_TOKEN])) {
				$accessToken = $param[self::AUTH_TOKEN];
			}
		}
		if (empty($accessToken) && C("IS_DEBUG")) {
			$accessToken = C('AUTH_TOKEN');
		}
		if (empty($accessToken)) {
			throw_exception("AuthToken不能为空", $type = 'ThinkException', $code = 0);
		}
		try {
			$infoJson = rtrim(base64_decode($accessToken), "\0");
		} catch (\Phalcon\Exception $e) {
			throw_exception("AuthToken已经失效。", $type = 'ThinkException', $code = 0);
		}
		$info = json_decode($infoJson, true);

		if (json_last_error() != 0) {
			throw_exception("AuthToken已经失效", $type = 'ThinkException', $code = 0);
		}
		$this->userId     = $info['buyer_id'];
		$this->wx_open_id = $info['wx_open_id'];
	}

	/**
	 * @param flag success CURD 操作成功
	 * @param array $data 具体返回信息
	 * Function descript: 返回带参数，标志信息，提示信息的json 数组
	 *
	 */
	function returnApiSuccess($data = array())
	{
		$result = array(
			'flag'   => 'Success',
			'result' => $data
		);
		print json_encode($result);
	}

	/**
	 * @param null $msg 返回具体错误的提示信息
	 * @param flag success CURD 操作失败
	 * Function descript:返回标志信息 ‘Error'，和提示信息的json 数组
	 */
	function returnApiError($msg = null)
	{
		$result = array(
			'flag' => 'Error',
			'msg'  => $msg,
		);
		print json_encode($result);
	}

	/**
	 * @param flag success CURD 操作失败
	 * Function descript:返回标志信息 ‘Error'，和提示信息，当前系统繁忙，请稍后重试；
	 */
	function returnApiErrorExample()
	{
		$result = array(
			'flag' => 'Error',
			'msg'  => '当前系统繁忙，请稍后重试！',
		);
		print json_encode($result);
	}

	/**
	 * 获取接口请求内容
	 */
	public function getRequest()
	{
		$postStr = $this->getCurlJson();
		if (!is_array($this->_postValue)) {
			if (!empty($postStr)) {
				//json传递格式
				$this->_postValue = json_decode($postStr, true);
			} else {
				//直接post变量格式
				$this->_postValue = $postStr;
			}
		}

		return $this->_postValue;
	}

	public function getCurlJson()
	{
		return file_get_contents("php://input");
	}

	/**
	 * $_POST
	 */
	public function getPost()
	{
		return I("post.");
	}

	/**
	 * $_GET
	 */
	public function getQueryParam($value = '')
	{
		return I("get." . $value);
	}

	/**
	 * @ 把post参数变成字符串
	 * @param $params post参数
	 * @return null|string
	 */
	public function assemble($params)
	{
		if (!is_array($params)) {
			return '';
		}
		ksort($params);
		$paramstr = '';
		foreach ($params as $key => $value) {
			if ($value == "") {
				continue;
			} else {
				$paramstr .= $key . (is_array($value) ? $this->assemble($value) : $value);
			}
		}

		return $paramstr;
	}
}