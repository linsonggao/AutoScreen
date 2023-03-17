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
//获取汉字首字母拼音
function chineseCharacter($zh)
{
    $ret = '';
    $s1 = iconv('UTF-8', 'GBK//IGNORE', $zh);
    $s2 = iconv('GBK', 'UTF-8', $s1);
    if ($s2 == $zh) {
        $zh = $s1;
    }
    for ($i = 0; $i < mb_strlen($zh); $i++) {
        $s1 = mb_substr($zh, $i, 1);
        $p = ord($s1);
        if ($p > 160) {
            $s2 = mb_substr($zh, $i++, 2);
            $ret .= getfirstchar($s2);
        } else {
            $ret .= $s1;
        }
    }

    return $ret;
}

function getFirstChar($str)
{
    if (empty($str)) {
        return '';
    }

    $fir = $fchar = ord($str[0]);
    if ($fchar >= ord('A') && $fchar <= ord('z')) {
        return mb_strtoupper($str[0]);
    }

    $s1 = @iconv('UTF-8', 'gb2312//IGNORE', $str);
    $s2 = @iconv('gb2312', 'UTF-8', $s1);
    $s = $s2 == $str ? $s1 : $str;
    if (!isset($s[0]) || !isset($s[1])) {
        return '';
    }

    $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;

    if (is_numeric($str)) {
        return $str;
    }
    if (($asc >= -20319 && $asc <= -20284) || $fir == 'A') {
        return 'A';
    }
    if (($asc >= -20283 && $asc <= -19776) || $fir == 'B') {
        return 'B';
    }
    if (($asc >= -19775 && $asc <= -19219) || $fir == 'C') {
        return 'C';
    }
    if (($asc >= -19218 && $asc <= -18711) || $fir == 'D') {
        return 'D';
    }
    if (($asc >= -18710 && $asc <= -18527) || $fir == 'E') {
        return 'E';
    }
    if (($asc >= -18526 && $asc <= -18240) || $fir == 'F') {
        return 'F';
    }
    if (($asc >= -18239 && $asc <= -17923) || $fir == 'G') {
        return 'G';
    }
    if (($asc >= -17922 && $asc <= -17418) || $fir == 'H') {
        return 'H';
    }
    if (($asc >= -17417 && $asc <= -16475) || $fir == 'J') {
        return 'J';
    }
    if (($asc >= -16474 && $asc <= -16213) || $fir == 'K') {
        return 'K';
    }
    if (($asc >= -16212 && $asc <= -15641) || $fir == 'L') {
        return 'L';
    }
    if (($asc >= -15640 && $asc <= -15166) || $fir == 'M') {
        return 'M';
    }
    if (($asc >= -15165 && $asc <= -14923) || $fir == 'N') {
        return 'N';
    }
    if (($asc >= -14922 && $asc <= -14915) || $fir == 'O') {
        return 'O';
    }
    if (($asc >= -14914 && $asc <= -14631) || $fir == 'P') {
        return 'P';
    }
    if (($asc >= -14630 && $asc <= -14150) || $fir == 'Q') {
        return 'Q';
    }
    if (($asc >= -14149 && $asc <= -14091) || $fir == 'R') {
        return 'R';
    }
    if (($asc >= -14090 && $asc <= -13319) || $fir == 'S') {
        return 'S';
    }
    if (($asc >= -13318 && $asc <= -12839) || $fir == 'T') {
        return 'T';
    }
    if (($asc >= -12838 && $asc <= -12557) || $fir == 'W') {
        return 'W';
    }
    if (($asc >= -12556 && $asc <= -11848) || $fir == 'X') {
        return 'X';
    }
    if (($asc >= -11847 && $asc <= -11056) || $fir == 'Y') {
        return 'Y';
    }
    if (($asc >= -11055 && $asc <= -10247) || $fir == 'Z') {
        return 'Z';
    }

    return '';
}
