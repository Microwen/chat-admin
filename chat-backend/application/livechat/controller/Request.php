<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 11:28
 */

namespace app\livechat\controller;
require_once __DIR__ . '/../../../vendor/workerman/workerman/Lib/Constants.php';

class Request
{
    /**
     * @return string
     * @throws \Exception
     */
    public function index() {
        $received = json_decode(base64_decode($_REQUEST['request']), true);
        return $this -> commander($received);
    }

    /**
     * @param $received
     * @return null|string
     * @throws \Exception
     */
    private function commander($received) {
        switch ($received["type"]) {
            case 'init':
                ConnectManager::conn($received['client_id'], $received['uuid']);
                break;
            case 'list':
                return json(ListManager::get($received['uuid']));
            case 'member':
                MemberManager::get($received['groupid']);
                break;
            case 'hist':
                break;
            case 'msg':
                MsgManager::send($received, $received['to']);
                break;
            case 'heart':
                break;
            default:
                return json_encode(array('code' => 1, 'msg' => 'unknown type'));
        }
        return null;
    }


}