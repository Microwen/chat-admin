<?php
namespace app\livechat\Model;

use think\Model;
use think\Db;

class GroupModel extends Model{

    /**
     * 创建组并返回组id
     * @param $groupName
     * @return string
     */
    public function createGroup($groupName) {
        $result = $this -> findGroup($groupName);
        if (!$result) {
            $this -> db -> insert('group') -> cols(array('id' => "GP".time().rand(100, 999), 'gourpname' => $groupName)) -> query();
        }
        return $result;
    }

    /**
     * 查找根据组名查找组id
     * @param $groupName
     * @return bool|string
     */
    public function findGroup($groupName) {
        $result = $this -> db -> select('id,groupname') -> from('group')
            -> where('groupname = :groupName') -> bindValue('groupName', $groupName)
            -> single();
        if (empty($result)) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * 保存用户聊天记录
     * @param $msg
     */
    public function saveMsg($msg) {
        $this -> db -> insert('msg') -> cols($msg) -> query();
    }

    /**
     * 根据组id取回组聊天记录
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getMsgByGroup($id) {
        return $this -> db -> select('msg.id,groupid,msg.uid,username,msg,format,send_time') -> from('msg')
            -> leftJoin('user_info', 'msg.uid = user_info.uuid')
            -> where('groupid = :id') -> bindValue('id', $id) -> orderByASC(array('msg.id')) -> query();
    }

    /**
     * 根据组id取回所有在组里的用户id
     * @param $id
     * @return mixed
     */
    public function getUserByGroup($groupid) {
        return $this -> db -> select('uid,groupid') -> from('user_to_group')
            -> where('groupid = :groupid') -> bindValue('groupid', $groupid) -> query();
    }

    public function getGroupsByUid($uid) {
        return Db::table('user_to_group') -> where('uid', $uid) -> column('groupid');
    }

    public function getGroupName($groupid) {
        return Db::table('group') -> where('id', $groupid) -> value('groupname');
    }
}