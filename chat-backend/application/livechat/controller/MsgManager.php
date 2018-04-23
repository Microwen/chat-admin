<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 12:48
 */
namespace app\Livechat\Controller;

require_once __DIR__.'/../../../vendor/workerman/gatewayclient/Gateway.php';
use GatewayClient\Gateway;
use app\livechat\Model\GroupModel;
use app\livechat\Model\UserModel;
use app\livechat\Model\MessageModel;
class MsgManager
{
    /**
     * @param $msg
     * @param $where
     * @throws \Exception
     */
    public static function send($msg) {
        Gateway::$registerAddress = '127.0.0.1:1238';
        $buff = array();
        $groupModel = new GroupModel();
        $userModel = new UserModel();
        $msgModel = new MessageModel();
        $buff['username'] = $msg['mine']['username'];
        $buff['avatar'] = $msg['mine']['avatar'];
        $buff['type'] = $msg['to']['type'];
        $buff['content'] = $msg['mine']['content'];
        $buff['timestamp'] = time() * 1000;
        if (!strcmp($msg['to']['type'], 'group')) {
            $buff['fromid'] = $msg['mine']['id'];
            $buff['id'] = $groupModel -> findGroupId($msg['to']['groupname']);
            Gateway::sendToGroup($buff['id'], json_encode($buff));
            foreach ($groupModel -> getMembers($buff['id']) as $m) {
                if (!Gateway::isUidOnline($m['uuid'])) {
                    self::suspend($buff, $m['uuid'], $msgModel);
                }
            }
        } else {
            $buff['id'] = $msg['mine']['id'];
            if (Gateway::isUidOnline($msg['to']['id'])){
                Gateway::sendToUid($msg['to']['id'], json_encode($buff));
            } else {
                self::suspend($buff, $msg['to']['id'], $msgModel);
            }
        }
        $msgModel -> saveMsg(
            //TODO
            array(
                'type' => $buff['type'],
                'uid' => '',
                'rec_uid' => '',
                'groupid' => '',
                'msg' => $msg['mine']['content'],
                'format' => 'txt',
                'send_time' => $buff['timestamp']
            )
        );
    }

    public static function suspend($buff, $rec, $msgModel) {
        $susp = array(
            'uid' => $buff['id'],
            'type' => $buff['type'],
            'rec_uid' => $rec,
            'groupid' => null,
            'msg' => $buff['content'],
            'format' => 'txt',
            'send_time' => $buff['timestamp']
        );
        if (!strcmp($buff['type'], 'group')) {
            $susp['groupid'] = $buff['id'];
            $susp['uid'] = $buff['fromid'];
        }
        $msgModel -> suspendMsg($susp);
    }

    public static function find($msg) {

    }

}