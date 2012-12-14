<?php
class UtilsHelper
{

    public static $sng = array('RU','UA','BY');
    public static $sortKey;
    public static $orderSort;


    /**
     * Function return first element of array, if array have only 1 element, else return all array.
     * @param Array $aArr
     */
    public static function normalizeArray($aArr)
    {
        if (count($aArr) == 1)
        {
            $aEach = each($aArr);
            return $aArr[$aEach['key']];
        } else
        {
            return $aArr;
        }
    }

    public static function formatXML($xml)
    {
        libxml_use_internal_errors(true);
        $oDM = new DOMDocument();
        try {
            $load = @$oDM->loadXML($xml);
            if ($load)
            {
                $oDM->formatOutput = true;
                $oDM->normalize();
                $xml = $oDM->saveXML();
            }
        }catch (Exeption $e ){

        }
        return $xml;
    }

    /**
     * Modify incoming parameter to array(wrap to array), incoming parameter is object
     * @param unknown_type $oSoapArray
     */
    public static function soapObjectsArray(&$oSoapArray)
    {
        if (!is_array($oSoapArray))
        {
            $oObj = $oSoapArray;
            $oSoapArray = array(
                $oObj
            );
        }
    }

    /**
     * Return real value of soap element.
     * @static
     * @param $element
     * @return mixed
     */
    public static function soapElementValue($element)
    {
        if (is_object($element))
        {
            return $element->_;
        } else
        {
            return $element;
        }
    }

    public static function dateToPointDate($sDate)
    {
        return date('d.m.Y', strtotime($sDate));
    }

    /**
     * Function for render duration time interval
     * @static
     * @param $sec - number of seconds
     * @param string $local - Locale for print (ru|en)
     * @return string - 5 ч 54 мин
     */
    public static function durationToTime($sec, $local = 'ru')
    {
        $min = (int)($sec / 60);
        $hour = floor($min / 60);
        $min = $min % 60;
        $hourWords = array('ru' => 'ч', 'en' => 'h');
        $minuteWords = array('ru' => 'мин', 'en' => 'min');
        return "{$hour} {$hourWords[$local]} {$min} {$minuteWords[$local]}";
    }

    /**
     * Функция которая добавляет правильное окончание к русскому слову использованному после числа.
     * @param $aWords - Массив из трех форм слова: первый элемент для 1, второй для 4, третий для 7, array('год','года','лет')
     * @param $iNum - число
     * @return string
     */
    public static function WordAfterNum($aWords, $iNum)
    {
        $iNum = $iNum % 100;
        if (count($aWords) > 2)
        {
            if ($iNum > 4 && $iNum < 21)
            {
                return $aWords[2];
            } else
            {
                $iOst = $iNum % 10;
                if ($iOst == 1)
                {
                    return $aWords[0];
                } elseif ($iOst > 1 && $iOst < 5)
                {
                    return $aWords[1];
                } else
                {
                    return $aWords[2];
                }
            }
        } else
        {
            if ($iNum > 1 && $iNum < 21)
            {
                return $aWords[1];
            } else
            {
                $iOst = $iNum % 10;
                if ($iOst == 1)
                {
                    return $aWords[0];
                } else
                {
                    return $aWords[1];
                }
            }
        }
    }

    public static function addLocalConsoleTask($taskName, $time)
    {
        $path = Yii::getPathOfAlias('site.console');
        $scriptPath = $path . '/yiic $taskname';

    }

    public static function countRussianCharacters($str)
    {
        mb_internal_encoding("UTF-8");
        $startCount = mb_strlen($str);
        $out = mb_ereg_replace("[а-яА-ЯЁё]", "", $str);
        $endCount = mb_strlen($out);
        return ($startCount - $endCount);
    }

    public static function fromTranslite($str)
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
        return strtr($str, $table);
    }

    public static function cityNameToRus($name, $fromCountryCode = 'EN')
    {
        if(in_array($fromCountryCode,self::$sng))
        {
            return self::fromTranslite($name);
        }
        return self::ruTranscript($name);
    }

    public static function ruTranscript($str)
    {
        $str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
        $table = array(
            'holly' => 'голли',
            'new' => 'нью',
            'island' => 'айленд',
            'york' => 'йорк',
            'saint' => 'санкт',
            'san' => 'сан',
            'ga' => 'га',
            'city' => 'сити',
            'wash' => 'ваш',
            'ing' => 'инг',
            'sey' => 'си',
            'ney' => 'ней',
            'john' => 'джон',
            'state' => 'штат',
            'idaho' => 'айдахо',
            'colu' => 'колу',
            'bia' => 'бия',
            'coa' => 'коа',
            'co' => 'ко',
            'ca' => 'ка',
            'well' => 'уел',
            'que' => 'кве',
            'el' => 'эл',
            'west' => 'вест',
            'south' => 'саус',
            'north' => 'норт',
            'east' => 'ист',
            'rise' => 'райз',
            'sun' => 'сан',
            'ha' => 'га',
            'mem' => 'мем',
            'smith' => 'смит',
            're' => 'ре',
            'both' => 'бот',
            'lake' => 'лайк',
            'ben' => 'бен',
            'town' => 'таун',
            'fren' => 'френ',
            'burg' => 'бург',
            'mour' => 'мур',
            'black' => 'блек',
            'bridge' => 'бридж',
            'buck' => 'бак',
            'eye' => 'ай',
            'bull' => 'бул',
            'head' => 'хэд',
            'doug' => 'дуг',
            'year' => 'еар',
            'nyon' => 'ньон',
            'age' => 'ейдж',
            'met' => 'мет',
            'less' => 'лес',
            'per' => 'пер',
            'mount' => 'моунт',
            'ted' => 'тед',
            'ned' => 'нед',
            'red' => 'ред',
            'sed' => 'сед',
            'bad' => 'бэд',
            'yellow' => 'елоу',
            'happy' => 'хепи',
            'luck' => 'лак',
            'quin' => 'куин',
            'ger' => 'джер',
            'german' => 'герман',
            'poul' => 'пол',
            'false' => 'фелс',
            'kuk' => 'кук',
            'eagle' => 'игл',
            'village' => 'виладж',
            'james' => 'джеймс',
            'stone' => 'стон',
            'rou' => 'роу',
            'ses' => 'сес',
            'fer' => 'фер',
            /*''=>'',
            ''=>'',
            ''=>'',
            ''=>'',
            ''=>'',
            ''=>'',
            ''=>'',/**/


            'you' => 'ю',
            'peace' => 'пис',
            'scotland' => 'скотланд',
            'day' => 'дей',
            'beach' => 'бич',
            'key' => 'кей',

            'jo' => 'джо',
            'je' => 'дже',
            'ee' => 'и',
            'oo' => 'у',

            'sh' => 'ш',
            'y' => 'и',
            'j' => 'дж',
            'ch' => 'ч',
            'a' => 'а',
            'b' => 'б',
            'c' => 'к',
            'd' => 'д',
            'e' => 'и',
            'f' => 'ф',
            'g' => 'г',
            'h' => 'х',
            'i' => 'и',
            'k' => 'к',
            'l' => 'л',
            'm' => 'м',
            'n' => 'н',
            'o' => 'о',
            'p' => 'п',
            'q' => 'ку',
            'r' => 'р',
            's' => 'с',
            't' => 'т',
            'u' => 'ю',
            'v' => 'в',
            'w' => 'в',
            'x' => 'кс',
            'z' => 'з',
        );
        $str = strtr($str, $table);
        unset($table);
        return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    }

    public static function ruSoundex($str)
    {

        $res = '';

        $literal = array();
        // ассоциативный массив букв
        // параметры звуков гласный / согласный

        // для гласных переход буквы в звук(и), редуцированный/нет, предполагаемые правила ударения исходя из кол-ва слогов (stressed syllable)
        // реализована проверка предполагаемого ударения

        // для согласных переход букв[ы] в звук(и), редуцируемый/нет, правила редуцирования

        // vowel
        $literal['А'] = array('status' => 'гласный', 'sound' => 'а', 'stressed' => 'а'); // никогда не меняется
        $literal['Е'] = array('status' => 'гласный', 'sound' => 'и', 'stressed' => 'э', 'АаЕеЁёИиОоУуЭэЮюЯяЬьЫыЪъ' => 'йэ'); // - особые правила, для этой буквы, стоящей после указанных, а также в начале слов
        $literal['Ё'] = array('status' => 'гласный', 'sound' => 'о', 'stressed' => 'о', 'АаЕеЁёИиОоУуЭэЮюЯяЬьЫыЪъ' => 'йо');
        $literal['И'] = array('status' => 'гласный', 'sound' => 'и', 'stressed' => 'и');
        $literal['О'] = array('status' => 'гласный', 'sound' => 'а', 'stressed' => 'о');
        $literal['У'] = array('status' => 'гласный', 'sound' => 'у', 'stressed' => 'у');
        $literal['Ы'] = array('status' => 'гласный', 'sound' => 'ы', 'stressed' => 'ы');
        $literal['Э'] = array('status' => 'гласный', 'sound' => 'э', 'stressed' => 'э');
        $literal['Ю'] = array('status' => 'гласный', 'sound' => 'у', 'stressed' => 'у', 'АаЕеЁёИиОоУуЭэЮюЯяЬьЫыЪъ' => 'йу');
        $literal['Я'] = array('status' => 'гласный', 'sound' => 'а', 'stressed' => 'а', 'АаЕеЁёИиОоУуЭэЮюЯяЬьЫыЪъ' => 'йа'); // заяц произносится как [зайец]
        $v_pattern = 'АаЕеЁёИиОоУуЭэЮюЯяЬьЫыЪъ';

        // кстати, надо добавить выкусывание гласных из концов слов, заканчивающихся на согласный-гласный-звонкий согласный (-ром, -лем, итд) гласная очень часто сглатывается
        // зы: это здесь не реализовано %)
        // проверено: soundex и сам с этим неплохо справляется

        // звонкие согласные редуцируются при удвоении.
        // звонкие согласные переходят в парный глухой перед глухим
        // глухие редуцируются полностью перед глухими.

        // consonant
        // в отличие от гласных, для согласных условие "стоит перед указанной или в конце слова"
        $literal['Б'] = array('status' => 'согласный', 'sound' => 'б', 'КкПпСсТтФфХхЦцЧчШшЩщ' => 'п');
        $literal['В'] = array('status' => 'согласный', 'sound' => 'в', 'КкПпСсТтФфХхЦцЧчШшЩщ' => 'ф');
        $literal['Г'] = array('status' => 'согласный', 'sound' => 'Г', 'КкПпСсТтФфХхЦцЧчШшЩщ' => 'к');
        $literal['Д'] = array('status' => 'согласный', 'sound' => 'д', 'КкПпСсТтФфХхЦцЧчШшЩщ' => 'т');
        $literal['Ж'] = array('status' => 'согласный', 'sound' => 'ж', 'КкПпСсТтФфХхЦцЧчШшЩщ' => 'ш');
        $literal['З'] = array('status' => 'согласный', 'sound' => 'з', 'КкПпСсТтФфХхЦцЧчШшЩщ' => 'с');
        $literal['Й'] = array('status' => 'согласный', 'sound' => 'й');
        $literal['К'] = array('status' => 'согласный', 'sound' => 'к', 'КкПпСсТтФфХхЦцЧчШшЩщ' => '');
        $literal['Л'] = array('status' => 'согласный', 'sound' => 'л');
        $literal['М'] = array('status' => 'согласный', 'sound' => 'м');
        $literal['Н'] = array('status' => 'согласный', 'sound' => 'н');
        $literal['П'] = array('status' => 'согласный', 'sound' => 'п', 'КкПпСсТтФфХхЦцЧчШшЩщ' => '');
        $literal['Р'] = array('status' => 'согласный', 'sound' => 'р');
        $literal['С'] = array('status' => 'согласный', 'sound' => 'с'); // а вот С не хочет редуцироваться, на первый взгляд...
        $literal['Т'] = array('status' => 'согласный', 'sound' => 'т', 'КкПпСсТтФфХхЦцЧчШшЩщ' => '');
        $literal['Ф'] = array('status' => 'согласный', 'sound' => 'ф', 'КкПпСсТтФфХхЦцЧчШшЩщ' => ''); // спорно
        $literal['Х'] = array('status' => 'согласный', 'sound' => 'х');
        $literal['Ц'] = array('status' => 'согласный', 'sound' => 'ц');
        $literal['Ч'] = array('status' => 'согласный', 'sound' => 'чь'); // всегда мягкий
        $literal['Ш'] = array('status' => 'согласный', 'sound' => 'ш');
        $literal['Щ'] = array('status' => 'согласный', 'sound' => 'щь');

        // спецсимволы
        $literal['Ъ'] = array('status' => 'знак', 'sound' => ' '); // только разделительный. делит жёстко
        $literal['Ь'] = array('status' => 'знак', 'sound' => 'ь'); // даже если делит, то мягко

        $literal['ТС'] = array('status' => 'сочетание', 'sound' => 'ц');
        $literal['ТЬС'] = $literal['ТС'];
        $literal['ШЬ'] = array('status' => 'сочетание', 'sound' => 'ш'); // всегда твёрдый. и это не единстенный рудимент языка

        $literal['СОЛНЦ'] = array('status' => 'сочетание', 'sound' => 'сонц');
        $literal['ЯИЧНИЦ'] = array('status' => 'сочетание', 'sound' => 'еишниц');
        $literal['КОНЕЧНО'] = array('status' => 'сочетание', 'sound' => 'канешно');
        $literal['ЧТО'] = array('status' => 'сочетание', 'sound' => 'што');
        $literal['ЗАЯ'] = array('status' => 'сочетание', 'sound' => 'зайэ'); // да-да. не только [зайэц], но и [зайэвльэнийэ]


        $sound = mb_convert_case($str, MB_CASE_UPPER, "UTF-8");

        // сначала сочетания
        foreach (array_filter($literal,
            create_function('$item', 'if( $item["status"] === "сочетание") return true; return false;'))
                 as $sign => $translate)
            $sound = str_replace($sign, $translate["sound"], $sound);

        // потом знаки
        foreach (array_filter($literal,
            create_function('$item', 'if( $item["status"] === "знак") return true; return false;'))
                 as $sign => $translate)
            $sound = str_replace($sign, $translate["sound"], $sound);


        // разделяем на слова, определяем кол-во слогов, заменяем ударный/безударный гласный (единственный или предполагая второй в двух-трёхсложном слове, предпредпоследний - в остальных)

        $words = preg_split('~[,.\~`1234567890-=\~!@#$%^&*()_+|{}\]\];:\'"<>/? ]~', $sound, -1, PREG_SPLIT_NO_EMPTY);

        // гласные
        foreach (array_filter($literal,
            create_function('$item', 'if( $item["status"] === "гласный") return true; return false;'))
                 as $sign => $translate)
        {
        // для каждого слова
            foreach ($words as &$word)
            {
                // кол-во гласных
                $vowel = preg_match_all("~[$v_pattern]~", $word, $del_me);
                // готовим
                $cur_pos = 0;
                $cur_vowel = 0;
                while (false !== $cur_pos = strpos($word, $sign, $cur_pos))
                {
                    $cur_vowel++;

                    if (sizeof($translate) == 4 && ($cur_pos === 0 || strpos($v_pattern, $word[$cur_pos - 1])))
                    {
                        $word = substr_replace($word, $translate[$v_pattern], $cur_pos, 1);
                    } elseif (1 == $vowel)
                        $word = substr_replace($word, $translate["stressed"], $cur_pos, 1); //
                    elseif (2 == $vowel && 1 == $cur_vowel)
                        $word = substr_replace($word, $translate["stressed"], $cur_pos, 1); // предполагаем, что в двухсложных словах первый слог ударный
                    elseif (3 <= $vowel && $cur_vowel == $vowel - 2)
                        $word = substr_replace($word, $translate["stressed"], $cur_pos, 1); // предполагаем, что слог ударный предпредпоследний
                    else
                        $word = substr_replace($word, $translate["sound"], $cur_pos, 1);
                    $cur_pos++;
                }
            }
        }

        $sound = implode($words, ' '); // клеим обратно

        // согласные
        foreach (array_filter($literal,
            create_function('$item', 'if( $item["status"] === "согласный") return true; return false;'))
                 as $sign => $translate)
        {
        // готовим
            $cur_pos = 0;
            while (false !== $cur_pos = strpos($sound, $sign, $cur_pos))
            {;
                if (sizeof($translate) == 3)
                {
                    $arrKeys = array_keys($translate);
                    $x = array_pop($arrKeys); // снимаем третий элемент
                    if (strpos($x, $sound[$cur_pos + 1]) || $cur_pos === strlen($sound))
                    {
                        $sound = substr_replace($sound, $translate[$x], $cur_pos, 1);
                    } elseif ($sound[$cur_pos] === $sound[$cur_pos + 1])
                        $sound = substr_replace($sound, $translate["sound"], $cur_pos, 2); // все двойные редуцируются
                    else
                        $sound = substr_replace($sound, $translate["sound"], $cur_pos, 1);

                } else
                {
                    $sound = substr_replace($sound, $translate["sound"], $cur_pos, 1);
                }

                $cur_pos++;
            }
        }
        // алес. фонемы привели к одному виду
        // дальше используем любой алгоритм для вычисления числового эквивалента

        // но остаётся сомнение. очень хочется расстаться с глухими предлогами перед глухими согласными ("к скалам")


        $sound = preg_replace('~[,.\~`1234567890-=\~!@#$%^&*()_+|{}\]\];:\'"<>/? ]~', '', $sound);
        $firstChar = mb_convert_case(mb_substr($str, 0, 1), MB_CASE_UPPER, "UTF-8");
        $firstChar = strtr($firstChar, array('А' => 'А', 'Я' => 'А', 'О' => 'А', 'Г' => 'Г', 'К' => 'Г', 'Х' => 'Г', 'Е' => 'Е', 'Ё' => 'Е', 'И' => 'Е', 'Э' => 'Е', 'У' => 'У', 'Ю' => 'У', 'Д' => 'Т', 'Ц' => 'Т', 'Т' => 'Т', 'Ч' => 'Т', 'В' => 'В', 'Ф' => 'В', 'Ж' => 'Ш', 'Ш' => 'Ш', 'Щ' => 'Ш', 'Б' => 'П', 'П' => 'П', 'З' => 'С', 'С' => 'С'));

        $res = $firstChar . substr(soundex(self::str_to_translit($sound)), 1);

        unset($literal);
        unset($sound);
        unset($translate);
        unset($words);
        return $res;
    }

    public static function str_to_translit($str)
    {
        return strtr($str, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => '?', 'ж' => '*', 'з' => 'z', 'и' => 'i', 'й' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => '4', 'ш' => 'w', 'щ' => 'w', 'ъ' => '"', 'ы' => 'y', 'ь' => '`', 'э' => 'e', 'ю' => 'u', 'я' => 'a'));
    }


    public static function soundex($str, $language = 'EN')
    {
        mb_internal_encoding("UTF-8");
        $str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
        if ($language == 'RU')
        {
            //$str = mb_ereg_replace("[^а-яА-ЯЁё]", "", $str);
            return self::ruSoundex($str);
        } else
        {
            //$str = mb_ereg_replace("[^a-zA-Z]", "", $str);
            return soundex($str);
        }
    }

    /**
     * На выходе получаем строку с убранными повторяюшимися символами, убранными гласныи
     * @static
     * @param $str
     * @return string
     */
    public static function ruMetaphone($str)
    {
        mb_internal_encoding("UTF-8");
        $str = mb_convert_case($str, MB_CASE_UPPER, "UTF-8");
        $str = mb_ereg_replace("[^А-ЯЁ\\ \\-]", "", $str);
        $str = strtr($str, array('А' => 'А', 'Я' => 'А', 'О' => 'А', 'Г' => 'Г', 'К' => 'Г', 'Х' => 'Г', 'Е' => 'Е', 'Ё' => 'Е', 'И' => 'Е','Й'=>'Е', 'Э' => 'Е', 'У' => 'У', 'Ю' => 'У', 'Д' => 'Т', 'Ц' => 'Ц', 'Т' => 'Т', 'Ч' => 'Т', 'В' => 'В', 'Ф' => 'В', 'Ж' => 'Ш', 'Ш' => 'Ш', 'Щ' => 'Ш', 'Б' => 'П', 'П' => 'П', 'З' => 'С', 'С' => 'С','-'=>' '));

        //убираем повторяющиеся символы
        $newStr = '';
        $lastChar = '';
        $n = mb_strlen($str);
        for($i=0;$i<$n;$i++){
            $chr = mb_substr($str, $i, 1);
            if($chr !== $lastChar)
            {
                $newStr .=$chr;
                $lastChar = $chr;
            }
        }
        $str = $newStr;

        $ret = strtr($str, array('ТС' => 'Ц', 'А' => '', 'Е' => '', 'У' => '','Ъ'=>'','Ь'=>''));
        return $ret ? $ret : $str;
    }



    /*
    * Расстояние между двумя точками
    * $latitude1, $longitude1 - широта, долгота 1-й точки,
    * $latitude2, $longitude2 - широта, долгота 2-й точки
    * Написано по мотивам http://gis-lab.info/qa/great-circles.html
    * Михаил Кобзарев <kobzarev@inforos.ru>
    *
    */
    public static function calculateTheDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {

        // перевести координаты в радианы
        $lat1 = $latitude1 * M_PI / 180;
        $lat2 = $latitude2 * M_PI / 180;
        $long1 = $longitude1 * M_PI / 180;
        $long2 = $longitude2 * M_PI / 180;

        // косинусы и синусы широт и разницы долгот
        $cl1 = cos($lat1);
        $cl2 = cos($lat2);
        $sl1 = sin($lat1);
        $sl2 = sin($lat2);
        $delta = $long2 - $long1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);

        // вычисления длины большого круга
        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

        //
        $ad = atan2($y, $x);
        $dist = $ad * 6372795; //6372795 - Earth radius

        return $dist;
    }


    /**
     * Canonize given hotel name
     *
     * @param string $name hotel name
     * @param string $city_name_latin city name for given hotel
     * @return string canonized hotel name
     */
    public static function canonizeHotelName($name, $city_name_latin)
    {
        // this could be in russian
        $name = mb_convert_case($name, MB_CASE_LOWER, "UTF-8");
        $name = self::str_to_translit($name);
        $city_name = strtolower($city_name_latin);
        // words to exclude from canonical name
        $exclude_words = array("hotel", $city_name);
        // remove non alnum from name
        $canonical_name = preg_replace("~[^a-z0-9\s]~", "", $name);
        // split by spaces
        $name_parts = preg_split("~[\s]+~", $canonical_name);

        // filter common words
        $filtered_parts = Array();
        foreach ($name_parts as $part) {
            if(in_array($part, $exclude_words))
                continue;
            $filtered_parts[]=$part;
        }
        // sort alphabetally
        sort($filtered_parts);
        // join
        $canonical_name = implode(" ", $filtered_parts);
        return trim($canonical_name);
    }

    private static function compareByKey($a, $b){
        if($a[self::$sortKey] == $b[self::$sortKey])
            return 0;
        return ($a[self::$sortKey] > $b[self::$sortKey] ? 1 : -1)*self::$orderSort;
    }

    private static function compareByMethod($a, $b){
        if($a->{self::$sortKey}() == $b->{self::$sortKey}())
            return 0;
        return ($a->{self::$sortKey}() > $b->{self::$sortKey}() ? 1 : -1)*self::$orderSort;
    }

    private static function compareByProperty($a, $b){
        if($a->{self::$sortKey} == $b->{self::$sortKey})
            return 0;
        return ($a->{self::$sortKey} > $b->{self::$sortKey} ? 1 : -1)*self::$orderSort;
    }

    /**
     * Sorting elements by key (key may be property, array key, or method)
     */
    public static function sortBy(&$sortingArray,$sortByKey,$orderAsc = true, $saveIndexes = false)
    {
        //
        //self::$sortingArray = null;
        self::$sortKey = $sortByKey;
        $function_name = '';
        self::$orderSort = $orderAsc ? 1 : -1;
        foreach($sortingArray as $firstElem)
            break;
        if(is_array($firstElem)){
            $function_name = 'UtilsHelper::compareByKey';
        }
        if(is_object($firstElem)){
            if(method_exists($firstElem,$sortByKey))
                $function_name = 'UtilsHelper::compareByMethod';
            else
                $function_name = 'UtilsHelper::compareByProperty';
        }
        if($saveIndexes)
            uasort($sortingArray,$function_name);
        else
            usort($sortingArray,$function_name);
    }

    public static function formatPrice($price){
        return number_format(ceil($price), 0, '', ' ');
    }
}
