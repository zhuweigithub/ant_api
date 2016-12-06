<?php

namespace Model\Dto;

class AuthToken
{

	/**
	 * @var 用户ID
	 */
	public $userId;

	/**
	 * @var 药师ID
	 */
	public $pharmacistId;

	/**
	 * @var 门店ID
	 */
	public $storeId;

	/**
	 * @var 门店店员ID
	 */
	public $storeUserId;


	public $phone;

	/**
	 * 特征值
	 * @var string
	 */
	public $ackCode;

	public $time;

	/**
	 *
	 * @var \Dto\Device
	 */
	private $device;

	public $siteId;

	/**
	 * @return Device
	 */
	public function getDevice()
	{
		return $this->device;
	}

	/**
	 * @param Device $device
	 */
	public function setDevice($device)
	{
		$this->device = $device;
	}

	public function generateAckCode()
	{
		unset($this->device->cv);

		return md5(json_encode($this->device));
	}
}