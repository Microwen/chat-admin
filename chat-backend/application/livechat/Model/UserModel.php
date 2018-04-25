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

    public static function delUser($uuid) {
        //TODO
    }
    /**
     * 查询用户
     * @param $uuid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUser($uuid) {
        return Db::table('user_info') -> where('uuid', $uuid) -> select();
    }

    /**
     * 根据uuid查询uid
     * @param $uuid
     * @return string
     */
    public function getUidByUuid($uuid) {
        return Db::table('user_info')->where('uuid',$uuid) -> value('uid');
    }

    /**
     * 根据uid得到uuid
     * @param $uid
     * @return mixed
     */
    public function getUUidByUid($uid) {
        return Db::table('user_info')->where('uid',$uid) -> value('uuid');
    }


    /**
     * 根据uuid查询用户所在的组
     * @param $uuid
     * @return mixed 用户的组
     */
    public function getGroupOfUser($uuid) {
        return $this -> db -> select('groupid, uuid') -> from('groupofuser')
            -> where('uuid = :uuid') -> bindValue('uuid', $uuid) -> query();
    }

    /**
     * 查询用户权限
     * @param $uuid
     * @return string 用户权限
     */
    public function getUserLevel($uuid) {
        return Db::table('user_info') -> where('uuid', $uuid) -> value('level');
    }

    /**
     * 查询用户名称
     * @param $uuid
     * @return string 用户名称
     */
    public function getUserName($uuid) {
        return Db::table('user_info') -> where('uuid',$uuid) -> value('username');
    }

    /**
     * 取回所有用户
     * @return mixed
     */
    public function get_all() {
        return $this -> db -> select('*') -> from('user_info') -> query();
    }


    /**
     * 获取好友分组
     * @param $uid
     * @return array
     */
    public function getFriendGroup($uid) {
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
    public function getFriends($uid) {
        return Db::table('user_to_user') -> field('list2 as list,username,uuid, avatar') ->  where('uid1',$uid) -> join('user_info', 'uid2=uid') -> union(function ($query) use ($uid) {
            $query -> table('user_to_user') -> field('list1 as list,username,uuid, avatar') ->  where('uid2',$uid) -> join('user_info', 'uid1=uid');}) -> select();

    }
}