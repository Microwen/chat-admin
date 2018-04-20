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
        self::mine($uuid, $return, $userModel);
        self::friend($uuid, $return, $userModel);
        self::group($uuid, $return, $userModel, $groupModel);
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

    private static function friend($uuid, &$return, $userModel) {
        $groups = $userModel -> getFriendGroup($userModel -> getUidByUuid($uuid));
        $count = 1;
        foreach ($groups as $foo) {
            $friends = $userModel -> getFriendInGroup($userModel -> getUidByUuid($uuid), $foo);
            $list = array(
                'groupname' => $foo,
                'id' => $count,
                'online' => 0,
                'list' => []
            );
            foreach ($friends as $bar) {
                array_push($list['list'], array(
                        'username' => $userModel -> getUserName($userModel -> getUUidByUid($bar)),
                        'id' => $userModel -> getUUidByUid($bar),
                        "avatar" => '',
                        "sign" => ''
                    )
                );
            }
            array_push($return['data']['friend'], $list);
        }
    }
    private static function group($uuid, &$return, $userModel, $groupModel) {
        $groupIds = $groupModel -> getGroupsByUid($userModel -> getUidByUuid($uuid));
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