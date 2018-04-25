<?php
namespace app\livechat\controller;
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/25
 * Time: 13:40
 */

use app\livechat\Model\UserModel;

class UserManager
{
    /**
     * 创建用户
     * @param $arr
     * @return bool
     */
    public static function add($arr) {
        if (!empty($arr['username'])) {
            $arr['uuid'] = md5(uniqid());
            $arr['create_time'] = date('Y-m-d H:m:s', time());
            UserModel::addUser($arr);
            return 1;
        } else {
            return 0;
        }
    }
}