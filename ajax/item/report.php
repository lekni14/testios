<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $abuseId = isset($_POST['abuseId']) ? $_POST['abuseId'] : 0;

        $itemId = helper::clearInt($itemId);
        $abuseId = helper::clearInt($abuseId);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $report = new report($dbo);
        $report->setRequestFrom(auth::getCurrentUserId());

        $result = $report->item($itemId, $abuseId);

        echo json_encode($result);
        exit;
    }
