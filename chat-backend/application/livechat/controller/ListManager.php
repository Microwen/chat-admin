<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 14:01
 */

namespace app\Livechat\Controller;

use app\livechat\Model\UserModel;
use app\livechat\Model\GroupModel;

class ListManager
{
    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get($uuid) {
        $return = array(
            'code' => 0,
            'msg' => '',
            'data' => array(
                'mine' => [],
                'friend' => [],
                'group' => []
            )
        );
        $uid = UserModel::getUidByUuid($uuid);
        self::mine($uuid, $return);
        self::friend($uid, $return);
        self::group($uid, $return);
        return $return;
    }

    /**
     * @param $uuid
     * @param $return
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function mine($uuid, &$return) {
        $user = UserModel::getUser($uuid);
        $return['data']['mine'] = array(
            'username' => $user[0]['username'],
            'id' => $uuid,
            "status" => "online",
            "sign" => '', //TODO
            "avatar" => $user[0]['avatar']
        );
    }

    /**
     * @param $uid
     * @param $return
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function friend($uid, &$return) {
        $groups = UserModel::getFriendGroup($uid);
        $friends = UserModel::getFriends($uid);
        $count = 1;
        foreach ($groups as $foo) {
            $list = array(
                'groupname' => $foo,
                'id' => $count,
                'list' => []
            );
            foreach ($friends as $bar) {
                if ($bar['list'] == $foo) {
                    array_push($list['list'], array(
                            'username' => $bar['username'],
                            'id' => $bar['uuid'],
                            "avatar" => $bar['avatar'],
                            "sign" => '' //TODO
                        )
                    );
                }
            }
            array_push($return['data']['friend'], $list);
            $count ++;
        }
    }

    /**
     * @param $uid
     * @param $return
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private static function group($uid, &$return) {
        $groups = GroupModel::getGroupsByUid($uid);
        foreach ($groups as $g) {
            $arr = array(
                'groupname' => $g['groupname'],
                'id' => $g['groupid'],
                "avatar" => ''//TODO
            );
            array_push($return['data']['group'], $arr);
        }
    }
}