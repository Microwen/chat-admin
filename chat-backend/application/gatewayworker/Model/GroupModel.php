<?php
namespace app\gatewayworker\Model;

use \GatewayWorker\Lib\Db;

class GroupModel {
    /**
     * GroupModel constructor.
     * @throws \Exception
     */
    public function __construct() {
        $this -> db = Db::instance('chat');
    }

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
        return $this -> db -> select('groupid,uid') -> from('user_to_group')
            -> where('uid = :uid') ->bindValue('uid', $uid) -> query();
    }

    public function getGroupName($groupid) {
        return $this -> db -> select('groupname,id') -> from('group')
            -> where('id = :id') -> bindValue('id', $groupid) -> single();
    }

    /**
     * 关闭数据库连接
     */
    public function close() {
        Db::close('chat');
    }
}