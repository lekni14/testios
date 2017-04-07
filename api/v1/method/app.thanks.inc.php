<?php

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");
    include_once($_SERVER['DOCUMENT_ROOT']."/config/api.inc.php");

    header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Licenses</title>

    <style type="text/css">

        html {
            background-color: #262626;
        }

        body {
            margin: 20px;
            color: #cacaca;
            width: 90%;
        }

        h2 {
            display: block;
            font-weight: bold;
            font-size: 16px;
        }

        h3 {
            margin-top: 20px;
            display: block;
            font-weight: normal;
            font-size: 14px;
        }

        p {
            margin-bottom: 5px;
            margin-top: 5px;
            display: block;
        }

    </style>

</head>
    <body>
        <h2>เครื่องมือที่ใช้ในการพัฒนา <?php echo APP_TITLE; ?></h2>

        <h3>มีดังนี้</h3>

        <p>Material Design, Icon, Css</p>
        <p>https://material.io</p>
        <br/>

        <p>Apache Version 2.0</p>
        <p>http://www.apache.org/licenses/LICENSE-2.0</p>
        <br/>

        <p>facebook-sdk</p>
        <p>https://developers.facebook.com/</p>
        <br/>

        <p>Java SE Development</p>
        <p>http://www.oracle.co/</p>
        <br/>

        <p>Google Firebase</p>
        <p>https://firebase.google.com/</p>
        <br/>

        <p>Google Map Api</p>
        <p>https://developers.google.com/maps/</p>
        <br/>

        และอื่นๆ อีกมากมาย
    </body>
</html>
