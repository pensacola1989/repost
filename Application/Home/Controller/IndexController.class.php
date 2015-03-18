<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $data = array("username", "username");

        $this->assign("data", $data);
        $this->display();
    }
    public function test(){
        $conf = C('LEVEL_APP');
        $title = $_POST['title'];
        $title = "goog你好";
        C("DB_PREFIX", 't_');
        $id = M('SellerActivityInfo')->where(array('SellerId' => 1))->save(array('Title' => $title));
        echo M()->getLastSql();
        var_dump($id);
        $this->assign('list', $id);
        //$this->display();
    }

    //数据库访问  http://localhost:8080/?a=dbdemo
    public function dbdemo(){
    	echo "这是数据库访问Demo。点击页面下方调试控制台SQL标签查看详细情况。";
        $mod = new \Think\Model();
        $r = $mod->query("show databases");
        $Data = M('Data'); // 实例化Data数据模型
        $Data->query("DROP TABLE IF EXISTS `mall_websetting`;");
        //建表
        $Data->query("
        			CREATE TABLE IF NOT EXISTS `think_data` (
        			`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
        			`data` varchar(255) NOT NULL,
        			PRIMARY KEY (`id`)
        			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;");
        //插入
        $Data->query("
        		   INSERT INTO `think_data` (`id`, `data`) VALUES
        		   (1, 'thinkphp'),
        		   (2, 'for'),
        		   (3, 'tae');");
        //查询
        $this->data = $Data->select();
        $this->display();
    }
    
    //缓存服务  http://localhost:8080/?a=cachedemo
    public function cachedemo(){
    	echo "注：需先在workStation开通Cache服务。"."<br />";
    	echo "1. 使用快捷函数S 进行缓存设置。"."<br />";
        echo S("中文12", "thinkphp4tae")."<br />";
        echo S("中文12")."<br />";
        
        echo "2. 直接使用cacheService进行缓存设置。"."<br />";
        echo $cacheService -> set('中文12', 'thinkphp4tae', 0)."<br />";
        echo $cacheService -> get('中文12').'<br>';
    }
    
    //smarty模板  http://localhost:8080/?a=smartydemo
    public function smartydemo(){
    	echo "这是smarty模板Demo。";
        $d = array("沉鱼", "落雁");
        $this->assign("data", $d);
        $this->assign("foo", "闭月羞花");
        $this->display();
    }
    
    public function filedemo(){
    	echo "注：需先在workStation开通FileStore服务。"."<br />";
    	echo "1. 使用快捷函数F 进行文件读写。"."<br />";
    	$content = '文件内容：12中文ejf#@%#';
    	$filename = 'thinkphptest';
    	F($filename,$content); //将$content保存到文件
    	 $filecontent=F($filename); //取文件的内容
    	 echo $filecontent."<br />";
    	 F($filename,null); //删除文件
        
        echo "2. 直接使用fileStoreService进行文件读写。"."<br />";
        $saveTextFileResult = $fileStoreService->saveTextFile("文件内容：12中文e","/services/tfs/thinkphp.txt") ;
        $getFileTextResult = $fileStoreService->getFileText("/services/tfs/thinkphp.txt") ;
        echo $getFileTextResult."<br />";
        $isFileExistResult = $fileStoreService->isFileExist("/services/tfs/thinkphp.txt") ;
        $deleteFileTextResult = $fileStoreService->deleteFile("/services/tfs/thinkphp.txt") ;
    }
    
    public function logdemo(){
    	echo "直接使用appLog 写日志。日志默认路径：logs\applogs。Log::record默认为DEBUG。";
    	$appLog->info("info-log-emssage");
        $appLog->warn("warn-log-emssage");
        $appLog->error("error-log-emssage");
        \Think\Log::record('测试日志信息');
    }
}