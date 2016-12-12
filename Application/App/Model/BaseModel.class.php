<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2016/12/09
 * Time: 11:23
 */
namespace App\Model;
use Think\Model;

class BaseModel extends Model {

	public function __construct(){

	}
	public function paramsVerify( $models ,$data = null ){
		if (!$models->create($data)){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			fb($models->getError());
		}else{
			return true;
		}
	}
}