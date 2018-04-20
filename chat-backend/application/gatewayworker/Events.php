<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \Workerman\Lib\Timer;
use \GatewayWorker\Lib\Gateway;
use app\gatewayworker\Model\GroupModel;
use app\gatewayworker\Model\UserModel;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    static protected $counts = array();
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */

    /**
     * GateWorker启动时开始定时器，并检测心跳
     * @param $businessWorker
     */
    public static function onWorkerStart($businessWorker) {
        $time_interval = 20;
        Timer::add($time_interval, array('Events', 'checkConn'),false);
    }

    /**
     * 客户端连接
     * @param $client_id
     */
    public static function onConnect($client_id) {
        // 向当前client_id发送数据 
        //Gateway::sendToClient($client_id, "Hello $client_id\n");
        Gateway::sendToClient($client_id, json_encode(array(
            'type'      => 'init',
            'client_id' => $client_id
        )));
        self::$counts[$client_id] = time();
    }
    

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     * @throws Exception
     */
   public static function onMessage($client_id, $message) {
        $message = json_decode($message, true);
        $buff = array(
            'type' => '',
            'status' => 0,
            'msg' => '',
            'uid' => '',
            'level' => '',
            'username' => '',
            'timestamp' => ''
        );
        $groupModel = new GroupModel();
        $userModel = new UserModel();
        switch ($message['type']) {
            case "msg":
            case "estb":
                if (empty($message['uid']) || empty($message['group'])){
                    goto ERR;
                }
                switch ($message['type']) {
                    case "estb":
                        self::estb_conn($client_id, $message, $userModel, $groupModel, $buff);
                        $buff['status'] = 1;
                        break;
                    case "msg":
                        self::format_msg($message, $userModel, $groupModel, $buff);
                        $buff['status'] = 1;
                        break;
                }
                $buff['timestamp'] = date('Y-m-d H:m:s', time());
                Gateway::sendToGroup($message['group'], json_encode($buff));
                break;
            case "heart":
                self::$counts[$client_id] = time();
                break;
            default:
                ERR:
                $buff['type'] = 'error';
                $buff['msg'] = "Format ERROR: ".$message['msg'];
                $buff['timestamp'] = date('Y-m-d H:m:s', time());
                Gateway::sendToClient($client_id, json_encode($buff));
                Gateway::closeClient($client_id);
                break;
        }
       $groupModel -> close();
       $userModel -> close();
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    * @throws Exception
    */
   public static function onClose($client_id) {
       // 向所有人发送
       echo $client_id."logout\n";
       unset(self::$counts[$client_id]);
       GateWay::sendToAll($client_id."logout");
   }



    /**
     * 建立连接后的处理
     * @param $client_id
     * @param $message
     * @param $userModel
     * @param $groupModel
     * @param $buff
     */
    private static function estb_conn($client_id, $message, $userModel, $groupModel, &$buff) {
        Gateway::bindUid($client_id, $message['uid']);
        Gateway::setSession($client_id,
            array(
                'uid' => $message['uid'],
                'level' => $userModel -> getUserLevel($message['uid']),
                'username' => $userModel -> getUserName($message['uid'])
            )
        );
        Gateway::joinGroup($client_id, $message['group']);
        $buff['type'] = 'join';
        $buff['msg'] = array(
            'members' => Gateway::getClientSessionsByGroup($message['group']),
            'count' => Gateway::getClientCountByGroup($message['group']));
        //历史记录发送
        Gateway::sendToClient($client_id, json_encode(array('type' => 'hist', 'cont' => $groupModel -> getMsgByGroup($groupModel -> findGroup($message['group'])))));
    }


    /**
     * 检查用户是否活跃，若没有，将连接强制断开
     */
   public static function checkConn() {
       foreach (self::$counts as $client_id => $lastTime) {
           $diff = time() - $lastTime;
           echo $diff;
           if ($diff > 90) {
               Gateway::closeClient($client_id);
               unset(self::$counts[$client_id]);
               echo $client_id."lost connection";
           }
       }
   }
}
