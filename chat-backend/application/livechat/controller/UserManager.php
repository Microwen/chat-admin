<?php
namespace app\livechat\controller;
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/25
 * Time: 13:40
 */

use app\livechat\Model\UserModel;
use think\Db;

class UserManager
{
    /**
     * 增加用户
     * @param $arr
     * @return array
     */
    public static function add($arr) {
        $ret = array();
        if (!empty($arr['username'])) {
            $arr['uuid'] = md5(uniqid());
            $arr['create_time'] = date('Y-m-d H:m:s', time());
            UserModel::addUser($arr);
            $ret['msg'] = 'User does not exist, added user.';
        }
        $ret['uid'] = UserModel::getUidByUsername($arr['username']);
        $ret['code'] = 0;
        return $ret;
    }

    /**
     * 微信用户接口
     * 若该用户不存在，则创建该用户
     * @param $arr
     * @return array
     */
    public static function wechatUser($arr) {
        if (empty($arr['openid']) || empty($arr['avatar']) || empty($arr['username'])) {
            return array('code' => 1, '格式错误');
        }
        $uid = UserModel::getWechatUser($arr['openid']);
        if (empty($uid)){
                $username = UserModel::getUUidByUsername($arr['username']);
                if (!empty($username)) {
                $arr['username'] = $arr['username']."".rand(0000,9999);
            }
            $user = self::add(array('username' => $arr['username'], 'avatar' => $arr['avatar'], 'pwd' => md5(uniqid()), 'platform' => 'Wechat'));
            UserModel::connectWechatUser($arr['openid'], $user['uid']);
        }
        return array('code' => 0, 'uuid' => UserModel::getWechatUser($arr['openid']));
    }
}