<?php
namespace app\livechat\Model;

use think\Model;
use think\Db;

class UserModel extends Model{

    /**
     * 创建用户
     * @param $arr array 用户数据(username & password必填，其他可选)
     * @return bool|mixed 返回false如果创建失败
     */
    public static function addUser($arr){
        return Db::table('user_info') -> insert($arr);
    }

    /**
     * 删除用户
     * @param $uuid
     */
    public static function delUser($uuid) {
        //TODO
    }

    /**
     * 更新用户信息
     */
    public static function updateInfo() {
        //TODO
    }

    public static function getAvatar($username) {
        return Db::table('user_info') -> where('username', $username) -> value('avatar');
    }

    /**
     * 根据openid获取uuid
     * @param $openid
     * @return mixed
     */
    public static function getWechatUser($openid) {
        return Db::table('wechat_to_user') -> join('user_info', "user_info.uid=wechat_to_user.uid")
            -> where('openid', $openid) -> value('user_info.uuid');
    }

    /**
     * 关联微信用户
     * @param $openid
     * @param $uid
     */
    public static function connectWechatUser($openid, $uid) {
        Db::table('wechat_to_user') -> insert(array('openid' => $openid, 'uid' => $uid));
    }
    /**
     * 查询用户
     * @param $uuid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUser($uuid) {
        return Db::table('user_info') -> where('uuid', $uuid) -> select();
    }

    /**
     * 根据uuid查询uid
     * @param $uuid
     * @return string
     */
    public static function getUidByUuid($uuid) {
        return Db::table('user_info')->where('uuid',$uuid) -> value('uid');
    }

    /**
     * 根据用户名得到uuid
     * @param $username
     * @return mixed
     */
    public static function getUUidByUsername($username) {
        return Db::table('user_info')->where('username',$username) -> value('uuid');
    }

    /**
     * 根据用户名得到uid
     * @param $username
     * @return mixed
     */
    public static function getUidByUsername($username) {
        return Db::table('user_info')->where('username',$username) -> value('uid');
    }

    /**
     * 获取用户密码
     * @param $uuid
     * @return mixed
     */
    public static function getPwd($uuid) {
        return Db::table('user_info') -> where('uuid', $uuid) -> value('pwd');
    }

    /**
     * 根据uuid查询用户所在的组
     * @param $uuid
     * @return mixed 用户的组
     */
    public static function getGroupOfUser($uuid) {
        //TODO
    }

    /**
     * 查询用户权限
     * @param $uuid
     * @return string 用户权限
     */
    public static function getUserLevel($uuid) {
        return Db::table('user_info') -> where('uuid', $uuid) -> value('level');
    }

    /**
     * 查询用户名称
     * @param $uuid
     * @return string 用户名称
     */
    public static function getUserName($uuid) {
        return Db::table('user_info') -> where('uuid',$uuid) -> value('username');
    }

    /**
     * 取回所有用户
     * @return mixed
     */
    public function get_all() {
        //TODO
    }


    /**
     * 获取好友分组
     * @param $uid
     * @return array
     */
    public static function getFriendGroup($uid) {
        return array_merge(Db::table('user_to_user') -> distinct(true) -> where('uid1',$uid) -> column('list2'),
            Db::table('user_to_user') -> distinct(true) -> where('uid2',$uid) -> column('list1')
            );
    }

    /**
     * 获取用户好友
     * @param $uid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getFriends($uid) {
        return Db::table('user_to_user') -> field('list2 as list,username,uuid, avatar') ->  where('uid1',$uid) -> join('user_info', 'uid2=uid') -> union(function ($query) use ($uid) {
            $query -> table('user_to_user') -> field('list1 as list,username,uuid, avatar') ->  where('uid2',$uid) -> join('user_info', 'uid1=uid');}) -> select();

    }
}