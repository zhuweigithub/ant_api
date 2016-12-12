<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2016/12/09
 * Time: 11:23
 */
namespace App\Model;
use Think\Model;

class UsersModel extends Model {
	const USERS     = 'users';
	const USERS_EXT = 'users_EXT';
	protected $_validate = array(
		array('wx_open_id', 'require', '微信openId不能为空！', Model::MUST_VALIDATE, 'regex', Model::MODEL_BOTH),
	);

	public function getGroups($where=array()){
		$map = array('status'=>1,'type'=>self::TYPE_ADMIN,'module'=>'admin');
		$map = array_merge($map,$where);
		return $this->where($map)->select();
	}

	/**
	 * 把用户添加到用户组,支持批量添加用户到用户组
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 *
	 * 示例: 把uid=1的用户添加到group_id为1,2的组 `AuthGroupModel->addToGroup(1,'1,2');`
	 */
	public function addToGroup($uid,$gid){
		$uid = is_array($uid)?implode(',',$uid):trim($uid,',');
		$gid = is_array($gid)?$gid:explode( ',',trim($gid,',') );

		$Access = M(self::AUTH_GROUP_ACCESS);
		if( isset($_REQUEST['batch']) ){
			//为单个用户批量添加用户组时,先删除旧数据
			$del = $Access->where( array('uid'=>array('in',$uid)) )->delete();
		}

		$uid_arr = explode(',',$uid);
		$uid_arr = array_diff($uid_arr,array(C('USER_ADMINISTRATOR')));
		$add = array();
		if( $del!==false ){
			foreach ($uid_arr as $u){
				//判断用户id是否合法
				if(M('Member')->getFieldByUid($u,'uid') == false){
					$this->error = "编号为{$u}的用户不存在！";
					return false;
				}
				foreach ($gid as $g){
					if( is_numeric($u) && is_numeric($g) ){
						$add[] = array('group_id'=>$g,'uid'=>$u);
					}
				}
			}
			$Access->addAll($add);
		}
		if ($Access->getDbError()) {
			if( count($uid_arr)==1 && count($gid)==1 ){
				//单个添加时定制错误提示
				$this->error = "不能重复添加";
			}
			return false;
		}else{
			return true;
		}
	}

	/**
	 * 返回用户所属用户组信息
	 * @param  int    $uid 用户id
	 * @return array  用户所属的用户组 array(
	 *                                         array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
	 *                                         ...)
	 */
	static public function getUserGroup($uid){
		static $groups = array();
		if (isset($groups[$uid]))
			return $groups[$uid];
		$prefix = C('DB_PREFIX');
		$user_groups = M()
			->field('uid,group_id,title,description,rules')
			->table($prefix.self::AUTH_GROUP_ACCESS.' a')
			->join ($prefix.self::AUTH_GROUP." g on a.group_id=g.id")
			->where("a.uid='$uid' and g.status='1'")
			->select();
		$groups[$uid]=$user_groups?$user_groups:array();
		return $groups[$uid];
	}

}

