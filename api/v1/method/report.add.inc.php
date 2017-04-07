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

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $toItemId = isset($_POST['toItemId']) ? $_POST['toItemId'] : 0;
    $abuseId = isset($_POST['abuseId']) ? $_POST['abuseId'] : 0;

    $toItemType = isset($_POST['toItemType']) ? $_POST['toItemType'] : 0;

    $toItemId = helper::clearInt($toItemId);
    $abuseId = helper::clearInt($abuseId);

    $toItemType = helper::clearInt($toItemType);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $report = new report($dbo);
    $report->setRequestFrom($accountId);

    $result = $report->add($toItemId, $toItemType, $abuseId);

    echo json_encode($result);
    exit;
}
