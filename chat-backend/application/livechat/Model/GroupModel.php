<?php
namespace app\livechat\Model;

use think\Model;
use think\Db;

class GroupModel extends Model{

    /**
     * 创建群并返回组id
     * @param $groupName
     * @return string
     */
    public static function createGroup($groupName) {
        Db::table('groups') -> insert(array('groupname' => $groupName));
        return self::findGroupId($groupName);
    }

    /**
     * 解散群
     */
    public static function dismiss() {
        //TODO
    }

    /**
     * 将用户加入群
     * @param $groups
     */
    public static function joinGroup($arr) {
        $arr['join_time'] = date('Y-m-d H:m:s', time());
        Db::table('user_to_group') -> insert($arr);
    }

    /**
     * 将用户退群
     * @param $arr
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function quitGroup($arr) {
        Db::table('user_to_group') -> where('uid', $arr['uid']) -> where('groupid', $arr['groupid']) ->delete();
    }

    /**
     * 查找根据群名查找群id
     * @param $groupName
     * @return bool|string
     */
    public static function findGroupId($groupName) {
        return Db::table('groups') -> where('groupname', $groupName) -> value('gid');
    }

    /**
     * 根据群id取回所有在群里的用户
     * @param $groupid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMembers($groupid) {
        return Db::table('user_to_group') -> join('user_info', "user_to_group.uid = user_info.uid", 'left') -> where('groupid', $groupid) -> select();
    }

    /**
     * 获取用户所加入的所有群
     * @param $uid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getGroupsByUid($uid) {
        return Db::table('user_to_group') -> field('groupid,groupname')-> where('uid', $uid) -> join('groups', 'groupid = gid') -> select();
    }

    /**
     * 获取群的名称
     * @param $groupid
     * @return mixed
     */
    public static function getGroupName($groupid) {
        return Db::table('groups') -> where('gid', $groupid) -> value('groupname');
    }
}