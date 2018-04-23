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

    /**
     * 保存未发消息
     * @param $msg
     */
    public function suspendMsg($msg) {
        Db::table('unsent_msg') -> insert($msg);
    }

    /**
     * 保存用户聊天记录
     * @param $msg
     */
    public function saveMsg($msg) {
        Db::table('msg') ->insert($msg);
    }

    /**
     * 根据群id取回组聊天记录
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getMsgByGroup($id) {
        Db::table('msg') -> field('msg.id,groupid,msg.uid,username,msg,format,send_time') -> join('user_info', 'msg.uid = user_info.uuid', 'left')
            -> where('groupid', $id) -> order('msg.id', 'asc') -> select();
    }

}