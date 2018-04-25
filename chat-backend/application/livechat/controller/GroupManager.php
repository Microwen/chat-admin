<?php
namespace app\livechat\controller;
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/25
 * Time: 13:40
 */

use app\livechat\Model\GroupModel;
use app\livechat\Model\UserModel;

class GroupManager
{
    /**
     * 创建群
     * @param $groupName
     * @return bool|string
     */
    public static function create($groupName) {
        $groupModel = new GroupModel();
        if ($groupModel -> findGroupId($groupName)) {
            return 'exist';
        } else {
            return $groupModel -> createGroup($groupName);
        }
    }

    /**
     * 加入群
     * @param $arr
     * @return bool
     */
    public static function join($arr) {
        $userModel = new UserModel();
        foreach ($arr as $v) {
            $v['uid'] = $userModel -> getUidByUuid($v['uuid']);
            unset($v['uuid']);
            GroupModel::joinGroup($v);
        }
        return 1;
    }

    public static function quit($arr) {
        $userModel = new UserModel();
        foreach ($arr as $v) {
            $v['uid'] = $userModel -> getUidByUuid($v['uuid']);
            unset($v['uuid']);
            GroupModel::quitGroup($v);
        }
        return 1;
    }
}