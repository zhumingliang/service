<?php
/**
 * 支付pay 服务 公共API
 */
namespace app\controller;

use app\business\AuthBusiness;

class AuthBase extends ApiBase {

    public $appId = "";
    public $token = "";
    public $time = 0;
    public function initialize() {
        parent::initialize();
        $this->appId = input("param.appid", "", "trim");
        $this->token = input("param.token", "", "trim");
        $this->time = input("param.time", 0, "intval");
        if(!$this->appId || !$this->token || !$this->time) {
            //$this->show("appid,token,time字段不能为空");
        }
//        (new AuthBusiness())->checkAuth($appId,$token,$time);
    }



}
