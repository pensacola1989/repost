<?php namespace Home\Controller;
/**
 * Created by PhpStorm.
 * User: weiwei
 * Date: 3/16/2015
 * Time: 3:59 PM
 */
use Think\Controller;
use Home\Service\Task;
use Home\Service\Queue;
use Home\Service\TaskQueue;

class WeiboController extends Controller {

    const APP_SECRET = '8d3b5c8fd40e0644347cc71b0bcedef7';

    public function console()
    {
        $taskQueue = new TaskQueue();

        $taskQueue
            ->enqueue(new Task(1))
            ->enqueue(new Task(2))
            ->enqueue(new Task(3))
            ->enqueue(new Task(4))
            ->enqueue(new Task(5));

        while ($taskQueue->getSize() != 0) {
            $task = $taskQueue->getNextTask();
            $task->run();

            sleep(3);
        }
    }

    public function index()
    {
        $this->assign('fuck', 'you');
        $this->display();
    }

    public function validate()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $appsecret= '8d3b5c8fd40e0644347cc71b0bcedef7';  //开发者的appsecret
        $tmpArr = array($appsecret, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        echo $_GET['echostr'];
        exit();
        if( $tmpStr == $signature ){
            // echo $_GET['echostr'];
            return true;
        }else{
            return false;
        }
    }

    public function handleMessage()
    {
        header( 'Content-type: text/html; charset=utf-8' );
       
        // if($this->validate() && isset($_GET['echostr'])) {
        //     echo $_GET['echostr'];
        // }
       
        $messageHandler = new \Org\Util\Weibo();
        $messageHandler->setAppSecret(self::APP_SECRET);
         
        $receieveMessage = $messageHandler->getPostMsgStr();
        // $receieveMessage = 'fuck';
        
        $returnStr = '';
        if (!empty($receieveMessage)) {

            //sender_id为发送回复消息的uid，即蓝v自己

            $sender_id = $receieveMessage['receiver_id'];

            //receiver_id为接收回复消息的uid，即蓝v的粉丝

            $receiver_id = $receieveMessage['sender_id'];



            //回复text类型的消息示例。

            $data_type = "text";

            $data = $messageHandler->textData("text消息回复测试");
        }

        $returnStr = $messageHandler->buildReplyMsg($receiver_id, $sender_id, $data, $data_type);
        \Think\Log::record($GLOBALS['HTTP_RAW_POST_DATA']  . '..........');
        echo json_encode($returnStr);
    }
}