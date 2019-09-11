<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 百度编辑器-控制器
// +----------------------------------------------------------------------
namespace addons\baiduue\controller;

//use think\facade\Config;
//use think\facade\Env;
//use easywcms\Easy;

class Index extends \app\admin\controller\Admin {

	//初始化
    public function initialize(){
		parent::initialize();
    }
	
	//入口
	public function run(){
		$action = $_GET['action'];
		switch ($action) {
			//配置参数
			case 'config':
				return $this->config();
				break;
			//上传图片
			case 'uploadimage':
				$Annex = new \app\admin\controller\Annex;
				$data = $Annex->setFile('upfile')->image();
				$data['state'] = 'SUCCESS';
				$data['url'] = $data['filepath'];
				exit(json_encode($data));
				break;
			//上传文件
			case 'uploadfile':
				return $this->baiduUeUpload();
				break;
			//上传视频
			case 'uploadvideo':
				break;
			//列出图片
			case 'listimage':
				//$result = include('action_list.php');
				break;
			/* 列出文件 */
			case 'listfile':
				//$result = include('action_list.php');
				break;
		
			/* 抓取远程文件 */
			case 'catchimage':
				//$result = include('action_crawler.php');
				break;
		
			default:
				$result = json_encode(array('state'=> '请求地址出错'));
				break;
		}
		
		/* 输出结果 */
		if (isset($_GET['callback'])) {
			if (preg_match('/^[\w_]+$/', $_GET['callback'])) {
				echo htmlspecialchars($_GET['callback']) . '(' . $result . ')';
			} else {
				echo json_encode(array(
					'state'=> 'callback参数不合法'
				));
			}
		} else {
			echo $result;
		}
	}
	
	//配置参数
	private function config(){
		$config = [
			/* 上传图片配置项 */
			'imageActionName'	=> 'uploadimage', //执行上传图片的action名称
			'imageFieldName'	=> 'upfile', // 提交的图片表单名称
			'imageMaxSize'		=> 11111111111, // 上传大小限制，单位B */
			'imageAllowFiles'	=> ['.png', '.jpg', '.jpeg', '.gif', '.bmp'], // 上传图片格式显示
			'imageCompressEnable'=> true, // 是否压缩图片,默认是true
			'imageCompressBorder'=> 1600, // 图片压缩最长边限制
			'imageInsertAlign'	=> 'none', // 插入的图片浮动方式
			'imageUrlPrefix'	=> '', //图片访问路径前缀
			//'imagePathFormat'=>'', //上传保存路径,可以自定义保存路径和文件名格式
						
//			/* 上传视频配置 */
//			'videoActionName'=>'uploadvideo', /* 执行上传视频的action名称 */
//			'videoFieldName'=>'upfile', /* 提交的视频表单名称 */
//			//'videoPathFormat'=>'', /* 上传保存路径,可以自定义保存路径和文件名格式 */
//			'videoUrlPrefix'=>'', /* 视频访问路径前缀 */
//			'videoMaxSize'=>$imageMaxSize, /* 上传大小限制，单位B，默认100MB */
//			'videoAllowFiles'=>array(
//				'.flv', '.swf', '.mkv', '.avi', '.rm', '.rmvb', '.mpeg', '.mpg',
//				'.ogg', '.ogv', '.mov', '.wmv', '.mp4', '.webm', '.mp3', '.wav', '.mid'), /* 上传视频格式显示 */
		
			/* 上传文件配置 */
			'fileActionName'=>'uploadfile', /* controller里,执行上传视频的action名称 */
			'fileFieldName'=>'upfile', /* 提交的文件表单名称 */
			//'filePathFormat'=>'', /* 上传保存路径,可以自定义保存路径和文件名格式 */
			'fileUrlPrefix'=>'', /* 文件访问路径前缀 */
			'fileMaxSize'=>11111111111, /* 上传大小限制，单位B，默认50MB */
			'fileAllowFiles'=>array(
				'.png', '.jpg', '.jpeg', '.gif', '.bmp',
				'.flv', '.swf', '.mkv', '.avi', '.rm', '.rmvb', '.mpeg', '.mpg',
				'.ogg', '.ogv', '.mov', '.wmv', '.mp4', '.webm', '.mp3', '.wav', '.mid',
				'.rar', '.zip', '.tar', '.gz', '.7z', '.bz2', '.cab', '.iso',
				'.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.pdf', '.txt', '.md', '.xml'
				), /* 上传文件格式显示 */
//		

		];
		
		return $config;
	}
	
	
}