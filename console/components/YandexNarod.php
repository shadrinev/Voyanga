<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 23.08.12
 * Time: 13:36
 */
class YandexNarod
{
    function uploadFile($login, $password, $filename)
    {
        $cookie_file = 'cookie.txt';
        $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6';

        // логинимся в систему
        $ch = curl_init('https://passport.yandex.ru/passport?mode=auth');

        $fields = array();
        $fields[] = "login=$login";
        $fields[] = "passwd=$password";
        $fields[] = "twoweeks=yes";
        curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] != 200)
        {
            echo date('H:i:s Y-m-d').' Error while authorization'.PHP_EOL;
            return false;
        }

        // запрашиваем сервер для загрузки файла
        $url = 'http://narod.yandex.ru/disk/getstorage/?rnd=' . (mt_rand(0, 777777) + 777777);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (preg_match('/"url":"(.*?)", "hash":"(.*?)", "purl":"(.*?)"/', $result, $m))
        {
            $upload_url = $m[1];
            $hash = $m[2];
            $purl = $m[3];
        }
        else
        {
            echo date('H:i:s Y-m-d').' Error while getting server to upload to'.PHP_EOL;
            return false;
        }

        // загружаем файл на сервер
        $url = $upload_url . '?tid=' . $hash;
        $fields = array();
        $fields['file'] = '@' . $filename;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, 'http://narod.yandex.ru/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] != 200)
        {
            echo date('H:i:s Y-m-d').' Error while uploading files'.PHP_EOL;
            return false;
        }

        $url = $purl . '?tid=' . $hash . '&rnd=' . (mt_rand(0, 777777) + 777777);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        $result = curl_exec($ch);

        if (!preg_match('/"status": "done"/', $result, $m))
        {
            echo date('H:i:s Y-m-d').' Error checking status'.PHP_EOL;
            return false;
        }

        // переходим на страницу и определяем ссылку
        $url = 'http://narod.yandex.ru/disk/last/';
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        if (preg_match('/<span class=\'b\-fname\'><a href="(.*?)">/', $result, $m))
        {
            $fileURL = trim($m[1]);
            return $fileURL;
        }

        echo date('H:i:s Y-m-d').' Error while determining link'.PHP_EOL;
        return false;
    }
}
