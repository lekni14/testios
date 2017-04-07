<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/config/api.inc.php");

if (!empty($_POST)) {

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;

    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $categoryId = isset($_POST['categoryId']) ? $_POST['categoryId'] : 0;
    $allowComments = isset($_POST['allowComments']) ? $_POST['allowComments'] : 0;

    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : 0;
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    $imgUrl = isset($_POST['imgUrl']) ? $_POST['imgUrl'] : '';
    
    $model = isset($_POST['model']) ? $_POST['model'] : '';
    $year = isset($_POST['year']) ? $_POST['year'] : '';
    $note = isset($_POST['note']) ? $_POST['note'] : '';

    $imgUrl2 = isset($_POST['imgUrl2']) ? $_POST['imgUrl2'] : '';
    $imgUrl3 = isset($_POST['imgUrl3']) ? $_POST['imgUrl3'] : '';
    $imgUrl4 = isset($_POST['imgUrl4']) ? $_POST['imgUrl4'] : '';

    $postArea = isset($_POST['postArea']) ? $_POST['postArea'] : '';
    $postCountry = isset($_POST['postCountry']) ? $_POST['postCountry'] : '';
    $postCity = isset($_POST['postCity']) ? $_POST['postCity'] : '';
    $postZipcode = isset($_POST['postZipcode']) ? $_POST['postZipcode'] : '';
    $postLat = isset($_POST['postLat']) ? $_POST['postLat'] : '0.000000';
    $postLng = isset($_POST['postLng']) ? $_POST['postLng'] : '0.000000';

    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);

    $itemId = helper::clearInt($itemId);

    $categoryId = helper::clearInt($categoryId);
    
    $price = helper::clearInt($price);
//    $price = helper::escapeText($price);
//    
//    $price  = number_format($price); 
    
    
    $allowComments = helper::clearInt($allowComments);

    $title = helper::clearText($title);
    $title = helper::escapeText($title);
    $title = preg_replace( "#(\\\r|\\\r\\\n|\\\n)#", " ", $title);
    
    $model = helper::clearText($model);
    $model = preg_replace( "#(\\\r|\\\r\\\n|\\\n)#", " ", $model);
    $model = helper::escapeText($model);
    
    $year = helper::clearText($year);
    $year = preg_replace( "#(\\\r|\\\r\\\n|\\\n)#", " ", $year);
    $year = helper::escapeText($year);
    
    $note = helper::clearText($note);
    $note = preg_replace( "#(\\\r|\\\r\\\n|\\\n)#", " ", $note);
    $note = helper::escapeText($note);
    
    $word_cut = array("มึง","กู","สันดาน","ควย","แตด","หี","fuck");
    $replace = "***";
    for ($i=0 ; $i<sizeof($word_cut) ; $i++) {
    $title= eregi_replace($word_cut[$i],$replace,$title); 
    }     //replace คำหยาบ

    $description = helper::clearText($description);

    $description = preg_replace( "#(\\\r|\\\r\\\n|\\\n)#", "<br>", $description); //replace all new lines to one new line
    //$description  = preg_replace('/\s+/', ' ', $description);        //replace all white spaces to one space
    
    for ($i=0 ; $i<sizeof($word_cut) ; $i++) {
    $description= eregi_replace($word_cut[$i],$replace,$description); 
    }     //replace คำหยาบ

    $description = helper::escapeText($description);

    $imgUrl = helper::clearText($imgUrl);
    $imgUrl = helper::escapeText($imgUrl);

    $imgUrl2 = helper::clearText($imgUrl2);
    $imgUrl2 = helper::escapeText($imgUrl2);

    $imgUrl3 = helper::clearText($imgUrl3);
    $imgUrl3 = helper::escapeText($imgUrl3);

    $imgUrl4 = helper::clearText($imgUrl4);
    $imgUrl4 = helper::escapeText($imgUrl4);

    $postArea = helper::clearText($postArea);
    $postArea = helper::escapeText($postArea);

    $postCountry = helper::clearText($postCountry);
    $postCountry = helper::escapeText($postCountry);

    $postCity = helper::clearText($postCity);
    $postCity = helper::escapeText($postCity);
    
    $postZipcode = helper::clearText($postZipcode);
    $postZipcode = helper::escapeText($postZipcode);

    $postLat = helper::clearText($postLat);
    $postLat = helper::escapeText($postLat);

    $postLng = helper::clearText($postLng);
    $postLng = helper::escapeText($postLng);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $item = new items($dbo);
    $item->setRequestFrom($accountId);

    $itemInfo = $item->info($itemId);

    if ($itemInfo['error'] === true) {

        return $result;
    }

    if ($itemInfo['fromUserId'] != $accountId) {

        return $result;
    }

    $result = $item->edit($itemId, $categoryId, $title, $imgUrl, $description, $allowComments, $price, $model, $year, $note);

    $images = new images($dbo);
    $images->setRequestFrom($accountId);

    $images->removeAll($itemId);

    if (strlen($imgUrl2) != 0) {

        $images->add($itemId, $imgUrl2, $imgUrl2, $imgUrl2);
    }

    if (strlen($imgUrl3) != 0) {

        $images->add($itemId, $imgUrl3, $imgUrl3, $imgUrl3);
    }

    if (strlen($imgUrl4) != 0) {

        $images->add($itemId, $imgUrl4, $imgUrl4, $imgUrl4);
    }

    echo json_encode($result);
    exit;
}
