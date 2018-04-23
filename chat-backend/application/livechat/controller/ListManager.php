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
            "sign" => '', //TODO
            "avatar" => '' //TODO
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
                'online' => 1, //TODO
                'list' => []
            );
            foreach ($friends as $bar) {
                if ($bar['list'] == $foo) {
                    array_push($list['list'], array(
                            'username' => $bar['username'],
                            'id' => $bar['uuid'],
                            "avatar" => '', //TODO
                            "sign" => '' //TODO
                        )
                    );
                }
            }
            array_push($return['data']['friend'], $list);
        }
    }

    private static function group($uid, &$return, $groupModel) {
        $groups = $groupModel -> getGroupsByUid($uid);
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