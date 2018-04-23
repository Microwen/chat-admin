<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/23
 * Time: 13:55
 */

namespace app\livechat\Model;

use think\Model;
use think\Db;

class MessageModel extends Model {


    public function suspendMsg($msg) {
        //TODO
    }

    /**
     * 保存用户聊天记录
     * @param $msg
     */
    public function saveMsg($msg) {
        //TODO
    }

    /**
     * 根据群id取回组聊天记录
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getMsgByGroup($id) {
        return $this -> db -> select('msg.id,groupid,msg.uid,username,msg,format,send_time') -> from('msg')
            -> leftJoin('user_info', 'msg.uid = user_info.uuid')
            -> where('groupid = :id') -> bindValue('id', $id) -> orderByASC(array('msg.id')) -> query();
    }

}