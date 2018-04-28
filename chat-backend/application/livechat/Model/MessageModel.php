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
    public static function suspendMsg($msg) {
        Db::table('unsent_msg') -> insert($msg);
    }

    /**
     * 保存用户聊天记录
     * @param $msg
     */
    public static function saveMsg($msg) {
        Db::table('msg') ->insert($msg);
    }

    /**
     * 取回未接收聊天记录
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function retMsg($uid) {
        return Db::table('unsent_msg') -> field('uuid,username,avatar,type,msg,groupid,send_time')-> join('user_info', 'unsent_msg.uid=user_info.uid', 'LEFT') -> where('rec_uid', $uid) -> select();
    }

    /**
     * 删除离线消息
     * @param $uid
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function delMsg($uid) {
        Db::table('unsent_msg') -> where('rec_uid', $uid) -> delete();
    }

    /**
     * 根据群id取回组聊天记录
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public static function getMsgByGroup($id) {
        Db::table('msg') -> field('msg.id,groupid,msg.uid,username,msg,format,send_time') -> join('user_info', 'msg.uid = user_info.uuid', 'left')
            -> where('groupid', $id) -> order('msg.id', 'asc') -> select();
    }

}