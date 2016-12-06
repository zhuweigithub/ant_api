<?php
namespace App\Model;
use Think\Model;
class UsersModel extends Model
{

	/**
	 * @var integer
	 */
	public $buyer_id;

	/**
	 * @var string
	 */
	public $mobile;

	/**
	 *
	 * @var string
	 */
	public $wx_open_id;

	/**
	 *
	 * @var string
	 */
	public $wx_union_id;

	/**
	 *
	 * @var string
	 */
	public $buyer_nick;

	/**
	 *
	 * @var string
	 */
	public $buyer_img;

	/**
	 *
	 * @var string
	 */
	public $province;

	/**
	 *
	 * @var string
	 */
	public $city;

	/**
	 *
	 * @var string
	 */
	public $password;

	/**
	 * @var integer
	 */
	public $sex;
	/**
	 *
	 * @var string
	 */
	public $email;

	/**
	 *
	 * @var integer
	 */
	public $register_time;

	/**
	 *
	 * @var integer
	 */
	public $register_clerks;

	/**
	 * @var string
	 */
	public $memo;
	/**
	 *
	 * @var string
	 */
	public $last_ipaddr;

	/**
	 *
	 * @var integer
	 */
	public $last_time;


	/**
	 *
	 * @var integer
	 */
	public $integrate;

	/**
	 *
	 * @var integer
	 */
	public $status;

}
