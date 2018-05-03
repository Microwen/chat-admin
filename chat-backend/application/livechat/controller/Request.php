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
     * 接口入口
     * @return string
     * @throws \Exception
     */
    public function index() {
        if (empty($_REQUEST['type'])) {
            return "欢迎使用金互通IM";
        }
        switch ($_REQUEST['type']) {
            case 'avatar':
                return json(UserManager::getAvatar($_REQUEST['username']));
            case 'login':
                return json(ConnectManager::login($_REQUEST['username'], $_REQUEST['pwd']));
            case 'init':
                return json(ConnectManager::conn($_REQUEST['client_id'], $_REQUEST['uuid']));
            case 'list':
                return json(ListManager::get());
            case 'member':
                return json(MemberManager::get($_REQUEST['id']));
            case 'hist':
                break;
            case 'msg':
                return json(MsgManager::send(json_decode($_REQUEST['data'], true)));
            case 'add':
                return json(UserManager::add(json_decode($_REQUEST['data'], true)));
            case 'del':
                return UserManager::remove(json_decode($_REQUEST['data'], true));
            case 'create':
                return GroupManager::create($_REQUEST['groupname']);
            case 'dismiss':
                return GroupManager::dismiss(json_decode($_REQUEST['data'], true));
            case 'join':
                return GroupManager::join(json_decode($_REQUEST['data'], true));
            case 'quit':
                return GroupManager::quit(json_decode($_REQUEST['data'], true));
            case 'wechat':
                return json(UserManager::wechatUser(json_decode($_REQUEST['data'], true)));
            default:
                return json_encode(array('code' => 1, 'msg' => '未知消息'));
        }
        return null;
    }
}