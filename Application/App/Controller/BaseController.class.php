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

	public function __construct(){
		$chars = 'ABCDEFGHJKMNPQRSTUVWXYZ0123456789ABCDEFGHJKMNPQRSTUVWXYZ0123456789ABCDEFGHJKMNPQRSTUVWXYZ0123456789';
		$chars = str_shuffle($chars);
		$this->access_token = sha1(substr($chars,0,32));
		$accessToken = $this->request->getHeader(GlobalConsts::AUTH_TOKEN);

		if (!$accessToken) {
			//从请求参数里面再去拿auth_token
			$accessToken = $this->request->getQuery(GlobalConsts::AUTH_TOKEN);

			if (!$accessToken) {
				//如果请求参数没有再去raw里面去拿auth_token
				$params = $this->request->getJsonRawBody(true);
				if (!empty($params[GlobalConsts::AUTH_TOKEN])) {
					$accessToken = $params[GlobalConsts::AUTH_TOKEN];
				}
			}
		}
		if (empty($accessToken) && config('common.isDebug')) {
			$accessToken = config('common.auth_token');
		}
		$authToken = new \Dto\AuthToken();
		try {
			$infoJson = rtrim($this->crypt->decryptBase64($accessToken), "\0");
		} catch (\Phalcon\Exception $e) {
			throw new HTTPException(ErrorCodeConsts::ERROR_CODE_USER_LOGIN_AUTHTOKEN_EXPIRED);
		}
		$info = json_decode($infoJson, true);
		if (json_last_error() != JSON_ERROR_NONE) {
			throw new HTTPException(ErrorCodeConsts::ERROR_CODE_USER_LOGIN_AUTHTOKEN_EXPIRED);
		}
		BeanUtil::copy($info, $authToken);
		$authToken->setDevice($this->device);
		$ackCode = $authToken->generateAckCode();
		if ($authToken->ackCode != $ackCode) {
			throw new HTTPException(ErrorCodeConsts::ERROR_CODE_USER_LOGIN_AUTHTOKEN_EXPIRED);
		}

		$this->userId = $authToken->userId;
		$this->pharmacist_id = $authToken->pharmacistId;
		$this->site_id = $authToken->siteId;
		$this->store_id = $authToken->storeId;
		$this->store_user_id = $authToken->storeUserId;
		$this->phone = $authToken->phone;

		if (!$this->site_id){
			$pharmacist = AccountService::getPharmacistInfoByUserId($this->userId);
			$this->site_id = $pharmacist->site_id;
		}else{
			//$log->info('onConstruct-----7------$authToken='.json_encode($authToken));
		}
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
				if((!isset($_POST[$k]))||(empty($_POST[$k]))){
					if($_POST[$k]!==0 && $_POST[$k]!=='0'){
						$this->returnApiError($k.'值为空！');
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
				if((!isset($_GET[$k]))||(empty($_GET[$k]))){
					if($_GET[$k]!==0 && $_GET[$k]!=='0'){
						$this->returnApiError($k.'值为空！');
					}
				}
			}
			unset($data);
			$data = I('get.');
			unset($data['_URL_'],$data['token']);
			return $data;
		}
	}
}