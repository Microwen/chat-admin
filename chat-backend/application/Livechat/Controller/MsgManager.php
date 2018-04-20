<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 12:48
 */
namespace app\Livechat\Controller;

use \GatewayWorker\Lib\Gateway;
use app\gatewayworker\Model\GroupModel;
use app\gatewayworker\Model\UserModel;

class MsgManager
{
    /**
     * @param $msg
     * @param $where
     * @throws \Exception
     */
    public static function send($msg, $where) {
        $buff = array();
        $groupModel = new GroupModel();
        $userModel = new UserModel();
        $buff['content'] = $msg['content'];
        $buff['timestamp'] = time() * 1000;
        switch ($where) {
            case 'group':
                $buff['username'] = $userModel -> getUserName($buff['fromid']);
                $buff['type'] = 'group';
                $buff['fromid'] = $msg['uuid'];
                $buff['id'] = $msg['gid'];
                Gateway::sendToGroup($msg['gid'], json_encode($buff));
                break;
            case 'client':
                $buff['username'] = $userModel -> getUserName($buff['uuid']);
                $buff['type'] = 'friend';
                $buff['id'] = $msg['uuid'];
                Gateway::sendToUid($msg['id'], json_encode($buff));
                break;
        }
        $groupModel -> saveMsg(
            array(
                'groupid' => $groupModel -> findGroup($msg['group']),
                'uid' => $userModel -> getUidByUuid($msg['uuid']),
                'msg' => $msg['msg'],
                'format' => $msg['format'],
                'send_time' => date('Y-m-d H:m:s', time())
            )
        );
        $groupModel -> close();
        $userModel -> close();
    }

    public static function find($msg) {

    }

}