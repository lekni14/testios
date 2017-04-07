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

    // Path to move uploaded files

    $uploaded_file = "";
    $uploaded_file_name = "";
    $uploaded_file_ext = "";

    $file_ext = "";

    // array for final json respone

    $response = array("error" => true);

    if (isset($_FILES['uploaded_file']['name'])) {

        $uploaded_file = $_FILES['uploaded_file']['tmp_name'];
        $uploaded_file_name = basename($_FILES['uploaded_file']['name']);
        $uploaded_file_ext = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);

        try {

            $time = time();
            if (!move_uploaded_file($_FILES['uploaded_file']['tmp_name'], "../../".TEMP_PATH."{$time}.".$uploaded_file_ext)) {

                // make error flag true
                $response['error'] = true;
                $response['message'] = 'Could not move the file!';
            }

            $imglib = new imglib($dbo);
            $response = $imglib->createPhoto("../../".TEMP_PATH."{$time}.".$uploaded_file_ext);
            unset($imglib);

            if ($response['error'] === false) {

                $account = new account($dbo, auth::getCurrentUserId());
                $account->setPhoto($response);

                auth::setCurrentUserPhotoUrl($response['lowPhotoUrl']);
            }

        } catch (Exception $e) {

            // Exception occurred. Make error flag true
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }
    }

    // Echo final json response to client
    echo json_encode($response);
