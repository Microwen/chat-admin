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
use app\livechat\Model\MessageModel;
class MsgManager
{
    /**
     * @param $msg
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function send($msg) {
        Gateway::$registerAddress = '127.0.0.1:1238';
        $buff = array(
            'username' => $msg['mine']['username'],
            'avatar' => $msg['mine']['avatar'],
            'type' => $msg['to']['type'],
            'content' => $msg['mine']['content'],
            'fromid' => $msg['mine']['id'],
            'timestamp' => time() * 1000
        );
        $groupModel = new GroupModel();
        $msgModel = new MessageModel();
        if (!strcmp($msg['to']['type'], 'group')) {
            $buff['id'] = $msg['to']['id'];
            Gateway::sendToGroup($buff['id'], json_encode($buff));
            foreach ($groupModel -> getMembers($buff['id']) as $m) {
                if (!Gateway::isUidOnline($m['uuid'])) {
                    self::saveMsg($buff, $m['uuid'], $msgModel, true);
                }
            }
            self::saveMsg($buff, null, $msgModel);
        } else {
            $buff['id'] = $msg['mine']['id'];
            if (Gateway::isUidOnline($msg['to']['id'])){
                Gateway::sendToUid($msg['to']['id'], json_encode($buff));
            } else {
                self::saveMsg($buff, $msg['to']['id'], $msgModel, true);
            }
            self::saveMsg($buff, $msg['to']['id'], $msgModel);
        }
    }

    public static function saveMsg($buff, $rec, $msgModel, $suspend = false) {
        $msg = array(
            'uid' => $buff['fromid'],
            'type' => $buff['type'],
            'rec_uid' => $rec,
            'groupid' => null,
            'msg' => $buff['content'],
            'format' => 'txt',
            'send_time' => date('Y-m-d H:m:s',$buff['timestamp']/1000)
        );
        if (!strcmp($buff['type'], 'group')) {
            $smsg['groupid'] = $buff['id'];
        }
        if ($suspend) {
            $msgModel -> suspendMsg($msg);
        } else {
            $msgModel -> saveMsg($msg);
        }
    }
}