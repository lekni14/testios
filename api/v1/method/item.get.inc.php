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

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;

    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);

    $itemId = helper::clearInt($itemId);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);


    $items = new items($dbo);
    $items->setRequestFrom($accountId);

    $itemInfo = $items->info($itemId);

    if ($itemInfo['error'] === false && $itemInfo['removeAt'] == 0) {

        $comments = new comments($dbo);
        $comments->setRequestFrom($accountId);

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "comments" => $comments->get($itemId, 0),
                        "items" => array(),
                        "images" => array());

        array_push($result['items'], $itemInfo);

        if ($itemInfo['imagesCount'] > 0) {

            $images = new images($dbo);
            $images->setRequestFrom($accountId);

            array_push($result['images'], $images->get($itemId));
        }
    }

    echo json_encode($result);
    exit;
}
