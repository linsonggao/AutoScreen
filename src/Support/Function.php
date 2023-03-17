<?php

declare(strict_types = 1);

use Illuminate\Contracts\Support\Arrayable;

if (!function_exists('dumps')) {
    /**
     * dump wrapper
     * PS: 自动对集合做toArray
     *
     * @param mixed $vars
     *
     * @return void
     */
    function dumps(mixed ...$vars): void
    {
        foreach ($vars as $var) {
            dump($var instanceof Arrayable ? $var->toArray() : $var);
        }
    }
}

if (!function_exists('dds')) {
    /**
     * dd wrapper
     * PS: 自动对集合做toArray
     *
     * @param mixed $vars
     *
     * @return void
     */
    function dds(mixed ...$vars): void
    {
        dumps(...$vars);
        exit(1);
    }
}
if (!function_exists('s')) {
    /**
     * @param mixed $data
     *
     * @return void
     */
    function s($data = [])
    {
        return response(['code' => 200, 'data' => $data]);
    }
}

function httpPost($url, $params)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    if (curl_errno($ch)) {
        dd(curl_error($ch)); //捕抓异常
    }

    $post_result = curl_exec($ch);
    curl_close($ch);

    return $post_result;
}
function httpGet($url, $params)
{
    $query = http_build_query($params); //json_encode($params);
    $url = $url . '?' . $query;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    if (curl_errno($ch)) {
        dd(curl_error($ch)); //捕抓异常
    }

    $post_result = curl_exec($ch);
    curl_close($ch);

    return $post_result;
}

/**
 * 获取身份证年龄
 *
 * @param string $idcard
 * @return int
 */
function get_idcard_age(string $idcard): int
{
    // 若是15位，则转换成18位
    if (15 == mb_strlen($idcard)) {
        $W = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
        $A = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        $s = 0;
        $idCard18 = mb_substr($idcard, 0, 6) . '19' . mb_substr($idcard, 6);
        $idCard18Len = mb_strlen($idCard18);
        for ($i = 0; $i < $idCard18Len; $i++) {
            $s = $s + mb_substr($idCard18, $i, 1) * $W[$i];
        }
        $idCard18 .= $A[$s % 11];
        $idcard = $idCard18;
    }

    $age = 0;
    $preg = "/^[1-9]\d{5}(18|19|20)(\d{2})((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/";
    if (preg_match($preg, $idcard, $matches)) {
        $birYear = "{$matches[1]}{$matches[2]}";
        $age = date('Y') - $birYear;
    }

    return $age;
}

if (!function_exists('get_idcard_year')) {
    /**
     * 根据身份证号码获取生日
     * @param string $idcard 身份证号码
     *
     * @return $birthday
     */

    function get_idcard_year(string $idcard): int
    {
        if (empty($idcard)) {
            return 0;
        }

        $bir = mb_substr($idcard, 6, 8);

        $year = (int) mb_substr($bir, 0, 4);

        return $year;
    }
}

if (!function_exists('get_sex')) {
    /**
     * 根据身份证号码获取性别
     *
     * @param string $idcard 身份证号码
     * @return string $sex 性别 1男 2女 0未知
     */
    function get_sex(string $idcard): string
    {
        if (empty($idcard)) {
            return '未知';
        }

        $sexint = (int) mb_substr($idcard, 16, 1);

        return $sexint % 2 === 0 ? '女' : '男';
    }
}

if (!function_exists('date_transition')) {
    /**
     * 时间转化
     *
     * @param $date
     * @return string|null
     */
    function date_transition($date)
    {
        return $date ? \Carbon\Carbon::make($date)->format('Y-m-d') : $date;
    }
}

if (!function_exists('str_middle_mask')) {
    /**
     * 掩盖字符串中间内容
     *
     * @param string $content
     * @param null|int $index
     * @param null|int $maxLength
     * @return string
     */
    function str_middle_mask(string $content, ?int $index = null, ?int $maxLength = null)
    {
        $index = $index ?? 1;
        $length = $maxLength ?? max(1, mb_strlen($content) - 2);

        return Str::mask($content, '*', $index, $length);
    }
}

if (!function_exists('get_idcard_sex')) {
    /**
     * 根据身份证号码获取性别
     *
     * @param string $idcard 身份证号码
     * @return string $sex 性别 1男 2女 0未知
     */
    function get_idcard_sex(string $idcard): string
    {
        if (empty($idcard)) {
            return '未知';
        }

        $sexint = (int) mb_substr($idcard, 16, 1);

        return $sexint % 2 === 0 ? '女' : '男';
    }
}
if (!function_exists('array_decode')) {
    /**
     * 解析字符串数组
     *
     * @param string $array
     * @param string $separator 解析的分隔符，默认为半角逗号
     * @return array
     */
    function array_decode(string $array, string $separator = ','): array
    {
        if (empty($array)) {
            return [];
        }

        return array_values(
            array_filter(
                explode($separator, $array),
                fn ($item) => '' !== $item
            )
        );
    }
}

if (!function_exists('get_idcard_year')) {
    /**
     * 根据身份证号码获取生日
     * @param string $idcard 身份证号码
     *
     * @return $birthday
     */

    function get_idcard_year(string $idcard): int
    {
        if (empty($idcard)) {
            return 0;
        }

        $bir = mb_substr($idcard, 6, 8);

        $year = (int) mb_substr($bir, 0, 4);

        return $year;
    }
}
