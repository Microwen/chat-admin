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
    public function createGroup($groupName) {
        $result = $this -> findGroupId($groupName);
        if (!$result) {
            $this -> db -> insert('groups') -> cols(array('id' => "GP".time().rand(100, 999), 'gourpname' => $groupName)) -> query();
        }
        return $result;
    }

    /**
     * 查找根据群名查找组id
     * @param $groupName
     * @return bool|string
     */
    public function findGroupId($groupName) {
        //TODO
//        $result = $this -> db -> select('id,groupname') -> from('groups')
//            -> where('groupname = :groupName') -> bindValue('groupName', $groupName)
//            -> single();
//        if (empty($result)) {
//            return false;
//        } else {
//            return $result;
//        }
    }

    /**
     * 根据群id取回所有在组里的用户
     * @param $groupid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMembers($groupid) {
        return Db::table('user_to_group') -> join('user_info', 'user_to_group.uid = user_info.uid', 'left') -> select();
    }

    /**
     * @param $uid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGroupsByUid($uid) {
        return Db::table('user_to_group') -> field('groupid,groupname')-> where('uid', $uid) -> join('groups', 'groupid = gid') -> select();
    }

    public function getGroupName($groupid) {
        return Db::table('groups') -> where('gid', $groupid) -> value('groupname');
    }
}