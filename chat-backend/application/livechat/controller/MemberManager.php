<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 15:16
 */

namespace app\Livechat\Controller;

use app\livechat\Model\GroupModel;

class MemberManager
{
    /**
     * @param $groupId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get($groupId) {
        $groupModel = new GroupModel();
        $members = $groupModel -> getMembers($groupId);
        $result = array(
            "code" => 0,
            "msg" => '',
            "data" => array(
                "list" => array(),
            )
        );
        foreach ($members as $v) {
            array_push($result['data']['list'], array(
                    "username" => $v['username'],
                    'id' => $v['uuid'],
                    'avatar' => $v['avatar'],
                    'sign' => ''//TODO
                )
            );
        }
        return $result;
    }
}