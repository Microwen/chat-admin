<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 13:32
 */

namespace app\livechat\controller;

require_once __DIR__.'/gatewayclient/Gateway.php';
use GatewayClient\Gateway;
use app\livechat\Model\UserModel;
use app\livechat\Model\GroupModel;

class ConnectManager
{
    /**
     * 登陆处理
     * @param $username
     * @param $pwd
     * @return array
     */
    public static function login($username, $pwd) {
        if (isset($username) && isset($pwd)) {
            $uuid = UserModel::getUUidByUsername($username);
            $stored = UserModel::getPwd($uuid);
            if ($stored == $pwd) {
                session('uuid',$uuid);
                return array("code" => 0, 'uuid' => $uuid);
            }
            return array("code" => 1);
        } else {
            return array("code" => 1);
        }
    }

    /**
     * Gatewayworker连接
     * @param $client_id
     * @return null
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function conn($client_id, $uuid) {
        if (!isset($uuid) && (session('uuid') != $uuid)) {
            return array("code" => 1, 'msg' => 'auth failed');
        }
        Gateway::$registerAddress = '127.0.0.1:1238';
        Gateway::bindUid($client_id, $uuid);
        Gateway::setSession($client_id,
            array(
                'uuid' => $uuid,
                'level' => UserModel::getUserLevel($uuid),
                'username' => UserModel::getUserName($uuid)
            )
        );
        $uid = UserModel::getUidByUuid($uuid);
        foreach (GroupModel::getGroupsByUid($uid) as $v) {
            Gateway::joinGroup($client_id, $v['groupid']);
        }
        MsgManager::retMsg($uid, $uuid);
        array("code" => 0);
    }
}