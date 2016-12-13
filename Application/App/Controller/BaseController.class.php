<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2016/11/52
 * Time: 11:52
 */
namespace App\Controller;
use Think\Controller;
class BaseController extends Controller {

	protected $userId;
	protected $access_token;
	protected $phone;
	protected $wx_open_id;
	private $_postValue = null;

	const AUTH_TOKEN = 'AuthToken';

	public function __construct(){
        //user控制器用于用户验证，返回token，所以无需校验
		if( strpos(get_class($this),'UserController') ){
			return;
		}
		$chars = 'ABCDEFGHJKMNPQRSTUVWXYZ0123456789ABCDEFGHJKMNPQRSTUVWXYZ0123456789ABCDEFGHJKMNPQRSTUVWXYZ0123456789';
		$chars = str_shuffle($chars);
		$this->access_token = sha1(substr($chars,0,32));
		$accessToken = I(self::AUTH_TOKEN);

		if (!$accessToken) {
			$this->returnApiError("AuthToken不能为空");
		}

		try {
			$infoJson = rtrim(base64_decode($accessToken), "\0");
		} catch (\Phalcon\Exception $e) {
			throw_exception("AuthToken已经失效", $type='ThinkException', $code=0);
		}
		$info = json_decode($infoJson, true);
		if (json_last_error() != 0) {
			throw_exception("AuthToken已经失效", $type='ThinkException', $code=0);
		}

		$this->userId = $info['userId'];
		$this->phone = $info['phone'];
		$this->wx_open_id = $info['wx_open_id'];
	}

	public function getApiUrl($url, $vars = '')
	{
		if (!(bool)$vars) {
			$vars = [ 'access_token' => $this->access_token];
		}
		if (!empty($vars)) {
			$url .= (stripos($url, '?') !== false) ? '&' : '?';
			$url .= (is_string($vars)) ? $vars : http_build_query($vars, '', '&');
		}

		return $url;
	}


	/**
	 * @param null $msg  返回正确的提示信息
	 * @param flag success CURD 操作成功
	 * @param array $data 具体返回信息
	 * Function descript: 返回带参数，标志信息，提示信息的json 数组
	 *
	 */
	function returnApiSuccess($msg = null,$data = array()){
		$result = array(
			'flag' => 'Success',
			'msg' => $msg,
			'data' =>$data
		);
		print json_encode($result);
	}

	/**
	 * @param null $msg  返回具体错误的提示信息
	 * @param flag success CURD 操作失败
	 * Function descript:返回标志信息 ‘Error'，和提示信息的json 数组
	 */
	function returnApiError($msg = null){
		$result = array(
			'flag' => 'Error',
			'msg' => $msg,
		);
		print json_encode($result);
	}

	/**
	 * @param flag success CURD 操作失败
	 * Function descript:返回标志信息 ‘Error'，和提示信息，当前系统繁忙，请稍后重试；
	 */
	function returnApiErrorExample(){
		$result = array(
			'flag' => 'Error',
			'msg' => '当前系统繁忙，请稍后重试！',
		);
		print json_encode($result);
	}

	/**
	 * @param null $data
	 * @return array|mixed|null
	 * Function descript: 过滤post提交的参数；
	 *
	 */

	function checkDataPost($data = null){
		if(!empty($data)){
			$data = explode(',',$data);
			foreach($data as $k=>$v){
				if((!isset($_POST[$v]))||(empty($_POST[$v]))){
					if($_POST[$v]!==0 && $_POST[$v]!=='0'){
						$this->returnApiError($v.'值为空！');
					}
				}
			}
			unset($data);
			$data = I('post.');
			unset($data['_URL_'],$data['token']);
			return $data;
		}
	}

	/**
	 * @param null $data
	 * @return array|mixed|null
	 * Function descript: 过滤get提交的参数；
	 *
	 */
	function checkDataGet($data = null){
		if(!empty($data)){
			$data = explode(',',$data);
			foreach($data as $k=>$v){
				if((!isset($_GET[$v]))||(empty($_GET[$v]))){
					if($_GET[$v]!==0 && $_GET[$v]!=='0'){
						$this->returnApiError($v.'值为空！');
					}
				}
			}
			unset($data);
			$data = I('get.');
			unset($data['_URL_'],$data['token']);
			return $data;
		}
	}

	public function getRequestParam($val = null, $strict = 0)
	{
		//return $postStr = $this->getPost($val);
		$data = $this->getRequest();
		if (!$val) {
			return $data;
		}
		if (isset($data[$val])) {
			if (is_array($data[$val])) {
				return $data[$val];
			} else {
				return trim($data[$val]);
			}

		}
		if ($strict) {
			return;
		}

		return '';
	}
	/**
	 * 获取接口请求内容
	 */
	public function getRequest()
	{
		$postStr = $this->getPost();
		dump($postStr);
		$postStr = $postStr[0];
		print_r($postStr);
		if (!is_array($this->_postValue)) {
			echo 11;
			if (!empty($postStr)) {
				echo 22;
				//json传递格式
				$this->_postValue = json_decode($postStr, true);
				echo 33;
				print_r($postStr);
			} else {
				//直接post变量格式
				$this->_postValue = $postStr;
			}
		}

		return $this->_postValue;
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
		return I("get.".$value);
	}

	/**
	 * @ 把post参数变成字符串
	 * @param $params post参数
	 * @return null|string
	 */
	private function assemble($params)
	{
		if (!is_array($params)){
			return '';
		}
		ksort($params);
		$paramstr = '';
		foreach ($params as $key => $value)
		{
			if ($value == ""){
				continue;
			}
			else {
				$paramstr .= $key . (is_array($value) ? $this->assemble($value) : $value);
			}
		}

		return $paramstr;
	}
}