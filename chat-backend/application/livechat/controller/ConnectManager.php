<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 13:32
 */

namespace app\Livechat\Controller;

require_once __DIR__.'/gatewayclient/Gateway.php';
use GatewayClient\Gateway;
use app\livechat\Model\UserModel;
use app\livechat\Model\GroupModel;
use app\Livechat\Controller\MsgManager;


class ConnectManager
{
    /**
     * @param $client_id
     * @param $uid
     * @throws \Exception
     */
    public static function conn($client_id, $uuid) {
        Gateway::$registerAddress = '127.0.0.1:1238';
        Gateway::bindUid($client_id, $uuid);
        Gateway::setSession($client_id,
            array(
                'uuid' => $uuid,
                'level' => UserModel::getUserLevel($uuid),
                'username' => UserModel::getUserName($uuid)
            )
        );
        foreach (GroupModel::getGroupsByUid(UserModel::getUidByUuid($uuid)) as $v) {
            Gateway::joinGroup($client_id, $v['groupid']);
        }

        MsgManager::retMsg($uuid);
    }
}