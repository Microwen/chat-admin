<?php
namespace app\livechat\Model;

use think\Model;
use Think\Db;

class UserModel extends Model {

    /**
     * 创建用户
     * @param $arr array 用户数据(username & password必填，其他可选)
     * @return bool|mixed 返回false如果创建失败
     */
    public function addUser($arr){
        if (!empty($arr['username']) && !empty($arr['pwd'])) {
            $arr['create_time'] = date('Y-m-d H:m:s', time());
            return $this -> db -> insert('user_info') -> cols($arr) -> query();
        } else {
            return false;
        }
    }

    /**
     * 查找用户
     * @param $uid
     * @return mixed 返回用户数据
     */
    public function findUser($uuid) {
        $user = $this -> db -> select('*') -> from('user_info')
            -> where('uuid = :uuid') -> bindValue('uuid', $uuid) -> row();
        return $user;
    }

    /**
     * 查询特定的列
     * @param $column string 需查询的列
     * @param $match string 匹配结果
     * @return mixed 返回查询到的数据
     */
    public function getUserBy($column, $match) {
        return $this -> db -> select('*') -> from('user_info')
            -> where (':column = :match') -> bindValues(array('column' => $column, 'match' => $match))
            -> query();
    }

    /**
     * 根据uuid查询uid
     * @param $uuid
     * @return string
     */
    public function getUidByUuid($uuid) {
        return $this -> db -> select('uid,uuid') -> from('user_info')
            -> where('uuid = :uuid') -> bindValue('uuid',  $uuid) ->single();
    }

    public function getUUidByUid($uid) {
        return $this -> db -> select('uuid,uid') -> from('user_info')
            -> where('uid = :uid') -> bindValue('uid',  $uid) ->single();
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
        return $this -> db -> select('level,uuid') -> from('user_info')
            -> where('uuid = :uuid') -> bindValue('uuid', $uuid) -> single();
    }

    /**
     * 查询用户名称
     * @param $uuid
     * @return string 用户名称
     */
    public function getUserName($uuid) {
        return $this -> db -> select('username,uuid') -> from('user_info')
            -> where('uuid = :uuid') -> bindValue('uuid', $uuid) -> single();
    }

    /**
     * 取回所有用户
     * @return mixed
     */
    public function get_all() {
        return $this -> db -> select('*') -> from('user_info') -> query();
    }


    public function getFriendGroup($uid) {
        return $this -> db -> select('list2') -> distinct() -> from('user_to_user')
            -> where('uid1 = :uid') ->bindValue('uid', $uid) -> union()
            -> select('list1') -> from('user_to_user')
            -> where('uid2 = :uid') -> bindValue('uid', $uid) ->query();
    }

    public function getFriendInGroup($uid, $group) {
        return $this -> db -> select('uid1,list1,uid2') -> from('user_to_user')
            -> where('uid2 = uid and list1 = :group') -> bindValues(array('uid' => $uid, 'group' => $group)) -> union()
            -> select('uid2,list2,uid1') -> from('user_to_user')
            -> where('uid1 = uid and list2 = :group') -> bindValues(array('uid' => $uid, 'group' => $group)) -> single();
    }

    /**
     * 关闭数据库连接
     */
    public function close() {
        Db::close('chat');
    }
}