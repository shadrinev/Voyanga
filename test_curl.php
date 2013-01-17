<?php

function str_to_translit($str)
{
    $table = array(
        'Shh' => 'Щ',
        'SHH' => 'Щ',
        'sh' => 'ш',
        'shh' => 'щ',
        'cow' => 'ква',
        'rya' => 'ря',
        'Ye' => 'Е',
        'Ya' => 'Я',
        'Ja' => 'Я',
        'ya' => 'я',
        'Ry' => 'Ры',
        'ry' => 'ры',
        'Sy' => 'Сы',
        'sy' => 'сы',
        'Ty' => 'Ты',
        'ty' => 'ты',
        'Ny' => 'Ны',
        'ny' => 'ны',
        'Vy' => 'Вы',
        'vy' => 'вы',
        'Dy' => 'Ды',
        'dy' => 'ды',
        'My' => 'Мы',
        'my' => 'мы',
        'Ky' => 'Кы',
        'ky' => 'кы',
        'iy' => 'ий',
        'yy' => 'ый',
        'Yo' => 'Ё',
        'YO' => 'Ё',
        'Zh' => 'Ж',
        'ZH' => 'Ж',
        'Kh' => 'Х',
        'KH' => 'Х',
        'Ch' => 'Ч',
        'CH' => 'Ч',
        'Sh' => 'Ш',
        'SH' => 'Ш',
        'yo' => 'ё',
        'jo' => 'ё',
        'zh' => 'ж',
        'Je' => 'Э',
        'JE' => 'Э',
        'Ju' => 'Ю',
        'Yu' => 'Ю',
        'JU' => 'Ю',
        'YU' => 'Ю',
        'kh' => 'х',
        'ch' => 'ч',
        'je' => 'э',
        'ju' => 'ю',
        'yu' => 'ю',
        'ja' => 'я',
        'W' => 'В',
        'w' => 'в',
        'A' => 'А',
        'B' => 'Б',
        'V' => 'В',
        'G' => 'Г',
        'D' => 'Д',
        'E' => 'Е',
        'Z' => 'З',
        'I' => 'И',
        'Y' => 'Й',
        'K' => 'К',
        'L' => 'Л',
        'M' => 'М',
        'N' => 'Н',
        'O' => 'О',
        'P' => 'П',
        'R' => 'Р',
        'S' => 'С',
        'T' => 'Т',
        'U' => 'У',
        'F' => 'Ф',
        'H' => 'Х',
        'C' => 'Ц',
        '\'' => 'ь',
        'a' => 'а',
        'b' => 'б',
        'v' => 'в',
        'g' => 'г',
        'd' => 'д',
        'e' => 'е',
        'z' => 'з',
        'i' => 'и',
        'y' => 'й',
        'k' => 'к',
        'l' => 'л',
        'm' => 'м',
        'n' => 'н',
        'o' => 'о',
        'p' => 'п',
        'r' => 'р',
        's' => 'с',
        't' => 'т',
        'u' => 'у',
        'f' => 'ф',
        'h' => 'х',
        'c' => 'ц',
    );
    $tr = array_flip($table);
    $tr['ы']='i';
    $tr['Ы']='i';
    $tr['ь']='';
    $tr['Ь']='';
    $tr['ъ']='';
    $tr['Ъ']='';
    $val = strtr($str, $tr);
    $val = str_replace(' ', '_', $val);
    return $val;
}

$rCh = curl_init();
$url = 'http://ya.ru/';

//if ($postData)
//{
//    curl_setopt($rCh, CURLOPT_POST, (true));
//}
curl_setopt($rCh, CURLOPT_HEADER, true);
curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
//if ($postData)
//{
//    curl_setopt($rCh, CURLOPT_POSTFIELDS, $postData);
//}
curl_setopt($rCh, CURLOPT_TIMEOUT, 80);
//$aHeadersToSend = array();
//$aHeadersToSend[] = "Content-Length: " . strlen($sRequest);
//$aHeadersToSend[] = "Content-Type: text/xml; charset=utf-8";
//$aHeadersToSend[] = "SOAPAction: \"$sAction\"";

//curl_setopt($rCh, CURLOPT_HTTPHEADER, $aHeadersToSend);

//evaluate get parametrs
/*if ($getData)
{
    $pos = strpos($url, '?');
    if ($pos !== false)
    {
        list($url, $args) = explode("?", $url, 2);
        parse_str($args, $params);
        $getData = array_merge($params, $getData);
    }

    $url = $url . '?' . http_build_query($getData);
}*/


curl_setopt($rCh, CURLOPT_URL, $url);

$sData = curl_exec($rCh);


if ($sData !== FALSE) {
    list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
    if (strpos($sHeaders, 'Continue') !== FALSE) {
        list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
    }
    echo "headers: " . $sHeaders;
    echo "response: " . $sData;


} else {
    echo "curlError:" . curl_error($rCh);
}
?>