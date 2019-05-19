<?php

namespace app\common;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Sms
{

    public static function sendSms($phone_no = '13074491521')
    {
        AlibabaCloud::accessKeyClient(config('app.aliyunsms.access_key_id'), config('app.aliyunsms.access_key_secret'))
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        try{
            $authCodeMT = mt_rand(100000,999999);
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'PhoneNumbers' => $phone_no,
                        'SignName' => "爱车送",
                        'TemplateCode' => "SMS_100885037",
                        'TemplateParam' => json_encode(['code'=>$authCodeMT, 'product' => 'dsd'], JSON_UNESCAPED_UNICODE),
                    ],
                ]);

            return $result->toArray();
        }catch(ClientException $e){
            echo $e->getErrorMessage() . PHP_EOL;
        }catch (ServerException $e){
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}

