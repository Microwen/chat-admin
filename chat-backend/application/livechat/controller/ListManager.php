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
    public static function get($uuid) {
        $return = array(
            'code' => 0,
            'msg' => '',
            'data' => array(
                'mine' => '',
                'friend' => [],
                'group' => []
            )
        );
        $userModel = new UserModel();
        $groupModel = new GroupModel();
        $uid = $userModel -> getUidByUuid($uuid);
        self::mine($uuid, $return, $userModel);
        self::friend($uid, $return, $userModel);
        self::group($uid, $return, $groupModel);
        return $return;
    }

    private static function mine($uuid, &$return, $userModel) {
        $return['data']['mine'] = array(
            'username' => $userModel -> getUserName($uuid),
            'id' => $uuid,
            "status" => "online",
            "sign" => '',
            "avatar" => ''
        );
    }

    private static function friend($uid, &$return, $userModel) {
        $groups = $userModel -> getFriendGroup($uid);
        $friends = $userModel -> getFriends($uid);
        $count = 1;
        foreach ($groups as $foo) {
            $list = array(
                'groupname' => $foo,
                'id' => $count,
                'online' => 0,
                'list' => []
            );
            foreach ($friends as $bar) {
                if ($bar['list'] == $foo) {
                    array_push($list['list'], array(
                            'username' => $bar['username'],
                            'id' => $bar['uuid'],
                            "avatar" => '',
                            "sign" => ''
                        )
                    );
                }
            }
            array_push($return['data']['friend'], $list);
        }
    }

    //TODO 优化group的查询
    private static function group($uid, &$return, $groupModel) {
        $groupIds = $groupModel -> getGroupsByUid($uid);
        foreach ($groupIds as $id) {
            $arr = array(
                'groupname' => $groupModel -> getGroupName($id),
                'id' => $id,
                "avatar" => ''
            );
            array_push($return['data']['group'], $arr);
        }
    }
}