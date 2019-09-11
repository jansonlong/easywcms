<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块- 统一请求 -逻辑层
// +----------------------------------------------------------------------
namespace app\common\logic;

class RequestCurlLogic {
	
    public $token = null;
	
	//初始化
    public function __construct(){
		$this->token = $_SERVER['HTTP_HOST'];
	}
	
    //请求服务器api接口获取jsonpCallback
    public function callback($store){
        //时间戳
        $timeStamp = time();
        //随机数
        $randomStr = self::createNonceStr();
        //生成签名
        $signature = self::arithmetic($timeStamp,$randomStr);
        //url地址
        $url = $store."/test/jsonpCallback/?time={$timeStamp}&rand={$randomStr}&sign={$signature}";
        return self::curl($url);
    }

    //随机生成字符串
    private function createNonceStr($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * @param $timeStamp 时间戳
     * @param $randomStr 随机字符串
     * @return string 返回签名
     */
    private function arithmetic($timeStamp,$randomStr){
        $arr['timeStamp'] = $timeStamp;
        $arr['randomStr'] = $randomStr;
        $arr['token'] = $this->token;
        //按照首字母大小写顺序排序
        sort($arr,SORT_STRING);
        //拼接成字符串
        $str = implode($arr);
        //进行加密
        $signature = sha1($str);
        $signature = md5($signature);
        //转换成大写
        $signature = strtoupper($signature);
        return $signature;
    }

	//发送请求
	public function curl($url,$data=false){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url); 
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Referer: ".$_SERVER['HTTP_HOST']));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if ($data) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 5);
		$result = curl_exec($curl);
		if (curl_errno($curl)) {return 'Errno'.curl_error($curl);}
		curl_close($curl);
		return json_decode($result,true); 
	}
}