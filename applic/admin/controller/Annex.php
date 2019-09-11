<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 附件上传-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\facade\Config;
use easywcms\Easy;
use OSS\OssClient;
use think\Image;

class Annex extends Admin {
	
	public $field = false;
	
	//初始化
    public function initialize(){
		//表单字段name值
		$field = $this->request->post('field');
		if($field){
			// 获取表单上传文件
			$this->file = $this->request->file($field);
		}
    }
	
	//
	public function setFile($field)
	{
		$this->field = $field;
		$this->file = $this->request->file($field);
		return $this;
	}
	
	//统一上传方法
	public function upload()
	{
		$type = $this->request->post('type');
		if( $type == 'image'){
			return $this->image();
		}else if( $type == 'file'){
			return $this->files();
		}
		return ['code'=>0,'msg'=>lang('Lack of parameters').' type'];
	}
	
	/**
	 * 存储文件信息到数据库
	 * @return json
	 * @date 2019-03-12
	 */
	private function saveData($data)
	{
		if( $data['code'] == 1 ){
			$Annex = new \app\admin\model\Annex;
			$Annex->allowField(true)->save($data);
		}
	}
	
	/**
	 * 图片上传
	 * @return json
	 * @date 2018-12-08
	 */
	public function image()
	{
        try{
            //获取配置参数
            $upload = Config::pull('upload');
            //获取Oss的配置
            $aliyunoss = Config::pull('aliyunoss');
            //判断是否开启上传到阿里oss
            if($aliyunoss['enable']==1){
                return $this->aliyunOSS($upload,$aliyunoss);
            }
            //获取上传文件的存放路径
            $url = parse_url( trim($upload['url']) );
            //移动到框架应用根目录
            $info = $this->file->validate(['size'=>$upload['size'],'ext'=>$upload['ext']])->move('.'.$url['path']);
            if($info){
                //是否开启水印功能
                if($upload['water']==1){
                    $fileObj = '.' . $url['path'] . str_replace('\\','/',$info->getSaveName());
                    //获取上传文件信息
                    $Image = Image::open($fileObj);
                    //判断是否符合添加水印的条件
                    if( $Image->width() >= $upload['minwidth'] && $Image->height() >= $upload['minheight'])
                    {
                        $Image->water($upload['source'],$upload['locate'],$upload['alpha'])->save($fileObj);
                    }
                }
                //文件信息
                $fileInfo = $info->getInfo();
                // 输出 图片类型
                $ext = $info->getExtension();
                // 输出 图片路径
                $path = $upload['url'] . str_replace('\\','/',$info->getSaveName());
                // 输出 图片名称
                $filename = $info->getFilename();
                //原文件名(删除扩展名)
                $fileTitle = str_replace('.'.$ext,'',$fileInfo['name']);
                $data = [
                    'code'		=> 1,
                    'ext'		=> $ext,
                    'host'		=> $upload['url'],
                    'filepath'	=> $path,
                    'filename'	=> $filename,
                    'filesize'	=> $fileInfo['size'],
                    'title'		=> $fileTitle
                ];
                $this->saveData($data);
                return $data;
            // 上传失败获取错误信息	
            }else{
                return ['code'=>0,'msg'=>$info->getError()];
            }
		} catch (\Exception $e) {
			return ['code'=>0,'msg'=>$e->getMessage()];
		}
	}
	
	/**
	 * 图片上传至阿里云OSS
	 * @date 2018-12-23
	 */
    public function aliyunOSS($upload,$aliyunoss)
	{
        try {
            //允许上传的文件后缀
            $file_ext = explode(',',$upload['ext']);
            //获取上传文件信息
            $Image = Image::open($this->file);
            //获取文件格式类型
            $ext = $Image->type();
            //判断上传文件类型
            if( ! in_array($ext, $file_ext) ){
                return ['code'=>0,'msg'=>lang('extensions to upload is not allowed')];
            }
            //文件详细信息
            $fileInfo = $this->file->getInfo();
            //判断上传文件大小
            if( $fileInfo['size'] > $upload['size'] ){
                return ['code'=>0,'msg'=>lang('filesize not match')];
            }
            //临时文件
            $tmp_name = $fileInfo['tmp_name'];
            //原文件名(删除扩展名)
            $fileTitle = str_replace('.'.$ext,'',$fileInfo['name']);
            $fileTitle = str_replace('.jpg','',$fileTitle);
            //是否开启水印功能
            if($upload['water']==1){
                //判断是否符合添加水印的条件
                if( $Image->width() >= $upload['minwidth'] && $Image->height() >= $upload['minheight'])
                {
                    $Image->water($upload['source'],$upload['locate'],$upload['alpha'])->save($tmp_name);
                }
            }
            // 执行阿里oss上传
			//是否对Bucket做了域名绑定，并且Endpoint参数填写的是自己的域名
			$isCName = false;
			if(!empty($aliyunoss['host'])){
				$aliyunoss['endpoint'] = $aliyunoss['host'];
				$isCName = 1;
			}
            //实例化对象 将配置传入
            $ossClient = new OssClient($aliyunoss['keyid'], $aliyunoss['keysecret'], $aliyunoss['endpoint'],$isCName);
            //这里是有sha1加密 生成文件名 之后连接上后缀
            $fileName = sha1(date('YmdHis', time()) . uniqid()) . '.' . $ext;
			//定义存放目录 默认日期 
			$filePath = $aliyunoss['directory'].'/'.date('Ymd', time()).'/'.$fileName;
            //执行阿里云上传
            $result = $ossClient->uploadFile($aliyunoss['bucket'], $filePath, $tmp_name);
			//删除加水印的临时文件
			if($upload['water']){ unlink($tmp_name); }
			//返回数据
			$data = [
				'code'		=> 1,
				'ext'		=> $ext,
				'host'		=> $aliyunoss['endpoint'],
				'filepath'	=> $result['info']['url'],
				'filename'	=> $fileName,
				'filesize'	=> $fileInfo['size'],
				'title'		=> $fileTitle,
				'type'		=> 2
			];
			$this->saveData($data);
			return $data;
        } catch (OSS\Core\OssException $e) {
			return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
	
	/**
	 * 文件上传
	 * @return json
	 * @date 2018-12-08
	 */
	public function files()
	{
		//获取配置参数
		$upload = Config::pull('upload');
		//文件详细信息
		$fileInfo = $this->file->getInfo();
		//获取文件后缀
		$ext = array_pop(explode('.',$fileInfo['name']));
		$fileInfo['ext'] = $ext;
		//判断文件后缀是否合法
		$extArray = explode(',',$upload['ext']);
		if( empty($ext) || ! in_array($ext,$extArray) ){
			return ['code'=>0,'msg'=>lang('extensions to upload is not allowed')];
		}
		//判断上传文件大小
		if( $fileInfo['size'] > $upload['size'] ){
			return ['code'=>0,'msg'=>lang('filesize not match')];
		}
		//存储文件信息
		$upload['fileInfo'] = $fileInfo;
		//获取Oss的配置
		$aliyunoss = Config::pull('aliyunoss');
		//判断是否开启上传到阿里oss
		if( $aliyunoss['enable'] == 1 ){
			return $this->aliyunOSSFile($upload,$aliyunoss);
		}else{
			//获取上传文件的存放路径
			$url = parse_url( trim($upload['url']) );
			//移动到框架应用根目录
			$info = $this->file->validate(['size'=>$upload['size'],'ext'=>$upload['ext']])->move('.'.$url['path']);
			if($info){
				// 输出 类型
				$ext = $info->getExtension();
				// 输出 路径
				$path = $upload['url'] . str_replace('\\','/',$info->getSaveName());
				// 输出 名称
				$filename = $info->getFilename();
				//原文件名(删除扩展名)
				$fileTitle = str_replace('.'.$ext,'',$this->file->getInfo()['name']);
				$data = [
					'code'		=> 1,
					'ext'		=> $ext,
					'host'		=> $upload['url'],
					'filepath'	=> $path,
					'filename'	=> $filename,
					'filesize'	=> $fileInfo['size'],
					'title'		=> $fileTitle
				];
				$this->saveData($data);
				return $data;
			// 上传失败获取错误信息	
			}else{
				return ['code'=>0,'msg'=>$this->file->getError()];
			}
		}

	}
	
	/**
	 * 文件上传至阿里云OSS
	 * @date 2019-03-12
	 */
    public function aliyunOSSFile($upload,$aliyunoss)
	{
		//临时文件
		$tmp_name = $upload['fileInfo']['tmp_name'];
		//文件后缀
		$ext = $upload['fileInfo']['ext'];
		//原文件名(删除扩展名)
		$fileTitle = str_replace('.'.$ext,'',$upload['fileInfo']['name']);
        //执行阿里oss上传
        try {
			//是否对Bucket做了域名绑定，并且Endpoint参数填写的是自己的域名
			$isCName = false;
			if(!empty($aliyunoss['host'])){
				$aliyunoss['endpoint'] = $aliyunoss['host'];
				$isCName = true;
			}
            //实例化对象 将配置传入
            $ossClient = new OssClient($aliyunoss['keyid'], $aliyunoss['keysecret'], $aliyunoss['endpoint'],$isCName);
            //这里是有sha1加密 生成文件名 之后连接上后缀
            $fileName = sha1(date('YmdHis', time()) . uniqid()) . '.' . $ext;
			//定义存放目录 默认日期 
			$filePath = $aliyunoss['directory'].'/'.date('Ymd', time()).'/'.$fileName;
            //执行阿里云上传
            $result = $ossClient->uploadFile($aliyunoss['bucket'], $filePath, $tmp_name);
			//删除加水印的临时文件
			if($upload['water']){ unlink($tmp_name); }
			//返回数据
			$data = [
				'code'		=> 1,
				'ext'		=> $ext,
				'host'		=> $aliyunoss['endpoint'],
				'filepath'	=> $result['info']['url'],
				'filename'	=> $fileName,
				'filesize'	=> $upload['fileInfo']['size'],
				'title'		=> $fileTitle,
				'type'		=> 2
			];
			$this->saveData($data);
			return $data;
        } catch (OSS\Core\OssException $e) {
			return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
	

	
}
