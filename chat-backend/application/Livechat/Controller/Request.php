<?php
/**
 * Created by PhpStorm.
 * User: GDB user
 * Date: 2018/4/20
 * Time: 11:28
 */

namespace app\Livechat\Controller;

use think\Controller;

class Request extends Controller
{
    /**
     * @throws \Exception
     */
    public function index() {
        $received = json_decode(base64_decode($_REQUEST['request']), true);
        $this -> commander($received);
    }

    /**
     * @param $received
     * @return null|string
     * @throws \Exception
     */
    private function commander($received) {
        switch ($received['type']) {
            case 'init':
                ConnectManager::conn($received['client_id'], $received['uuid']);
                break;
            case 'list':
                ListManager::get($received['uuid']);
                break;
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