<?php

namespace app\common;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Sms
{

    public static function sendSms($phone_no = '13074491521', $authCodeMT)
    {

        $app_key = config('aliyunsms.access_key_id');
        $app_secret = config('aliyunsms.access_key_secret');

        AlibabaCloud::accessKeyClient($app_key, $app_secret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        try{
//            $authCodeMT = mt_rand(100000,999999);
            $jsonTemplateParam = json_encode(['code'=>$authCodeMT]);

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'PhoneNumbers' => $phone_no,
                        'SignName' => "swoole直播赛事平台",
                        'TemplateCode' => "SMS_165414367",
                        'TemplateParam' => $jsonTemplateParam,
                    ],
                ])
                ->request();
//            print_r($result);
            return $result->toArray();
//            return $result;
        }catch(ClientException $e){
            echo $e->getErrorMessage() . PHP_EOL;
        }catch (ServerException $e){
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}

