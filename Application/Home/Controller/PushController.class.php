<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/12
 * Time: 下午2:28
 */
namespace Home\Controller;

use Think\Controller;

require "vendor/autoload.php";

use JPush\Model as M;
use JPush\JPushClient;
use JPush\JPushLog;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

class PushController extends Controller
{
    public function easy_push($id,$content){
        $br = '<br/>';
        $spilt = ' - ';
        $master_secret = '3dd1c77ac980516110338aa5';
        $app_key='ab36dbc34e0501604d25974e';
        JPushLog::setLogHandlers(array(new StreamHandler('jpush.log', Logger::DEBUG)));
        $client = new JPushClient($app_key, $master_secret);
        $json=array(
            'type'=>1
        );
        //echo $json;
        try {
            $result = $client->push()
                ->setPlatform(M\all)
                ->setAudience(M\alias(array($id)))
                ->setNotification(M\notification('Hi, JPush',M\android('hi,android', $title=null, $builder_id=null, $extras=$json)))
                ->printJSON()
                ->send();
            echo 'Push Success.' . $br;
            echo 'sendno : ' . $result->sendno . $br;
            echo 'msg_id : ' .$result->msg_id . $br;
            echo 'Response JSON : ' . $result->json . $br;
        } catch (APIRequestException $e) {
            echo 'Push Fail.' . $br;
            echo 'Http Code : ' . $e->httpCode . $br;
            echo 'code : ' . $e->code . $br;
            echo 'Error Message : ' . $e->message . $br;
            echo 'Response JSON : ' . $e->json . $br;
            echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
            echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
            echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
        } catch (APIConnectionException $e) {
            echo 'Push Fail: ' . $br;
            echo 'Error Message: ' . $e->getMessage() . $br;
            //response timeout means your request has probably be received by JPUsh Server,please check that whether need to be pushed again.
            echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
        }
    }
}