<?php
namespace App\Modules\Admin\WorkWechat;

use \Exception;
use EasyWeChat\Work\Message\Messenger;
use EasyWeChat\Work\Message\Client;
use \YC_Log;
use \EasyWeChat;
use EasyWeChat\Kernel\BaseClient;


class WorkWeChatModule
{
    //实例化消息发送类
    public static function getMessageInstance($key = null)
    {
 		$work    = EasyWeChat::work($key);
	    $client  = new Client($work);
		$messageObj = new Messenger($client);
		$messageObj->ofAgent($work->config->agent_id);
		return $messageObj;
    }

    /**
     * 发消息到人员
     * @param array|string $userIds
     * @param string $content
     * @return bool
     */
    public static function sendMsgToUsers($userIds, $content, $key = null)
    {
        if (empty($userIds) || empty($content)) {
            return true;
        }
	   	try {
			$messageObj = self::getMessageInstance($key);
			$messageObj->message($content);
	        $messageObj->toUser($userIds);
            $res = $messageObj->send();
            if(empty($res['invaliduser'])){
                return true;
            }
            return $res['invaliduser'];
	    } catch (Exception $e) {
	   	   YC_Log::info('[WorkWeChatModule sendMsgToUsers ] [%s] [%s]', $userIds, $content);
           throw new Exception($e->getMessage(), intval($e->getCode()) ?: 1000000);
	    }
    }

    /**
     * 发消息到部门
     * @param array|string $partyIds
     * @param string $content
     * @return bool
     */
    public static function sendMsgToParty($partyIds, $content)
    {
	   	try {
            $messageObj = self::getMessageInstance();
			$messageObj->message($content);
	        $messageObj->toParty($partyIds);
	        $messageObj->send();   	
	        return true;
	    } catch (Exception $e) {
            YC_Log::info('[WorkWeChatModule sendMsgToParty ] [%s] [%s]', $partyIds, $content);
	   	    throw new Exception($e->getMessage(), intval($e->getCode()) ?: 1000000);
	    }
    }

    /**
     * 发消息到标签
     * @param array|string $tagIds
     * @param string $content
     * @return bool
     */
    public static function sendMsgToTag($tagIds, $content)
    {
	   	try {
            $messageObj = self::getMessageInstance();
			$messageObj->message($content);
	        $messageObj->toTag($tagIds);
	        $messageObj->send();   	
	        return true;
	    } catch (Exception $e) {
            YC_Log::info('[WorkWeChatModule sendMsgToTag ] [%s] [%s]', $tagIds, $content);
	   	    throw new Exception($e->getMessage(), intval($e->getCode()) ?: 1000000);
	    }   	
    }

    public static function getUserByCode($code)
    {
        $workApp = EasyWeChat::work();
        $baseClient = new BaseClient($workApp);
        $resp = $baseClient->httpGet('cgi-bin/user/getuserinfo', ['code' => $code]);
        if ($resp['errcode'] != 0) {
            return false;
        }
        return htmlspecialchars($resp['UserId']);
    }

}







?>
