<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

    if (isset($_COOKIE['lang'])) {

        $language = $_COOKIE['lang'];

        $result = "th";

        if (in_array($language, $LANGS)) {

            $result = $language;
        }

        @setcookie("lang", $result, time() + 14 * 24 * 3600, "/");
        include_once($_SERVER['DOCUMENT_ROOT']."/lang/".$result.".php");

    }  else {

        $language = "th";


        $result = "th";

        if (in_array($language, $LANGS)) {

            $result = $language;
        }

        @setcookie("lang", $result, time() + 14 * 24 * 3600, "/");
        include_once($_SERVER['DOCUMENT_ROOT']."/lang/".$result.".php");
    }

    $LANG = $TEXT;
