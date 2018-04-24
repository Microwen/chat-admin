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
        switch ($_REQUEST['type']) {
            case 'init':
                ConnectManager::conn($_REQUEST['client_id'], $_REQUEST['uuid']);
                break;
            case 'list':
                return json(ListManager::get($_REQUEST['uuid']));
            case 'member':
                return json(MemberManager::get($_REQUEST['id']));
            case 'hist':
                break;
            case 'msg':
                MsgManager::send(json_decode($_REQUEST['data'], true));
                break;
            case 'heart':
                break;
            default:
                return json_encode(array('code' => 1, 'msg' => 'unknown type'));
        }
        return null;
    }
}