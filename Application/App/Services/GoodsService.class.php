<?php
/**
 * Created by zw
 * Email: 1191502428@qq.com
 * Date: 2017/03/01
 * Time: 22:20
 */
namespace App\Services;

class GoodsService
{

    private $_goodsDb;

    public function __construct()
    {
        $this->_goodsDb = D("Goods");
    }
    public function getGoodsList(){

    }


}

