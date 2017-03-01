<?php
namespace App\Controller;

use App\Services\UserService;
use Think\Controller;

class GoodsController extends BaseController
{

    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * 如果用户open_id存在则更新数据，不存在则添加，成功之后返回authtoken到前端，作为接口调用的统一凭据
     */
    public function login()
    {
        $params      = $this->getRequest();
        $wx_open_id  = $params['wx_open_id'];
        $wx_union_id = $params['wx_union_id'];
        $nickname    = $params['nickname'];
        $sex         = $params['sex'];
        $province    = $params['province'];
        $city        = $params['city'];
        $headimgurl  = $params['headimgurl'];
        if (empty($wx_open_id)) {
            return $this->returnApiError("请求参数错误！");
        }
        $result = $this->userService->userVerify($wx_open_id, $wx_union_id, $nickname, $sex, $province, $city, $headimgurl);
        if($result['statusCode'] == 0){
            return $this->returnApiSuccess($result['AuthToken']);
        }else{
            return $this->returnApiError($result['errMsg']);
        }

    }
}