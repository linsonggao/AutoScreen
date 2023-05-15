<?php

namespace Lsg\AutoScreen\Support;

use Cloudladder\Http\Client;

/**
 * @method static get(string $url,array $options,string $return) get方式请求，返回结果数组
 * @method static post(string $url,array $options,string $return) post方式请求，返回结果数组
 *
    ///RequestService::get($url,['query'=>['card_no'=>$card_no]]);
    //RequestService::post($url, ['json' => ['card_no' => $card_no]]);
 */
class RequestService
{
    private static $client;

    public static function __callStatic($name, $arguments)
    {
        static::$client = new Client();

        return self::request($name, ...$arguments);
    }

    private static function request($method, $url, $options, $return = 'data')
    {
        return
        collect(self::getData($url, $options, $method))->tap(function ($collection) {
            $collection->when($collection['code'] != 200, function ($ret) {
                throw new \Exception($ret['message'] ?? $ret['msg'] ?? var_export($ret));
            });
        })->get($return);
    }

    private static function getData($url, $options, $method)
    {
        return json_decode(static::$client->{$method}($url, $options)->getBody()->getContents(), true);
    }
}
