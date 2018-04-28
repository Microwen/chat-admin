<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 12:48
 */
namespace app\Livechat\Controller;

require_once __DIR__ . '/gatewayclient/Gateway.php';

use GatewayClient\Gateway;
use app\livechat\Model\GroupModel;
use app\livechat\Model\MessageModel;
class MsgManager
{
    /**
     * 发送消息
     * @param $msg
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function send($msg) {
        if (!self::chkMsg($msg)) {
            return array('code' => 1, 'msg' => 'msg check failed');
        }
        Gateway::$registerAddress = '127.0.0.1:1238';
        $buff = array(
            'username' => $msg['mine']['username'],
            'avatar' => $msg['mine']['avatar'],
            'type' => $msg['to']['type'],
            'content' => $msg['mine']['content'],
            'fromid' => $msg['mine']['id'],
            'timestamp' => time() * 1000
        );
        if (!strcmp($msg['to']['type'], 'group')) {
            $buff['id'] = $msg['to']['id'];
            Gateway::sendToGroup($buff['id'], json_encode(array('type' => 'msg', 'data' => $buff)));
            foreach (GroupModel::getMembers($buff['id']) as $m) {
                if (!Gateway::isUidOnline($m['uuid'])) {
                    self::saveMsg($buff, $m['uid'], true);
                }
            }
            self::saveMsg($buff, null);
        } else {
            $buff['id'] = $msg['mine']['id'];
            if (Gateway::isUidOnline($msg['to']['id'])){
                Gateway::sendToUid($msg['to']['id'], json_encode(array('type' => 'msg', 'data' => $buff)));
            } else {
                self::saveMsg($buff, $msg['to']['id'], true);
            }
            self::saveMsg($buff, $msg['to']['id']);
        }
        return array('code' => 0);
    }

    /**
     * 取回离线消息
     * @param $uid
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function retMsg($uid, $uuid) {
        $msg = MessageModel::retMsg($uid);
        $send = array();
        foreach ($msg as $m) {
            $sub = array(
                "id" => $m['uuid'],
                "username" => $m['username'],
                "avatar" => $m['avatar'],
                "type" => $m['type'],
                'content' => $m['msg'],
                'fromid' => $m['uuid'],
                'timestamp' => $m['send_time']
            );
            if (!strcmp($m['type'], "group")) {
                $buff['id'] = $m['groupid'];
            }
            array_push($send, $sub);
        }
        Gateway::sendToUid($uuid, json_encode(array('type' => 'ret', 'data' => $send)));
        MessageModel::delMsg($uid);
    }

    /**
     * 保存聊天记录
     * @param $buff
     * @param $rec
     * @param bool $suspend
     */
    public static function saveMsg($buff, $rec, $suspend = false) {
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
            $msg['groupid'] = $buff['id'];
        }
        if ($suspend) {
            MessageModel::suspendMsg($msg);
        } else {
            MessageModel::saveMsg($msg);
        }
    }

    private static function chkMsg($msg) {
        //TODO
        return true;
    }
}