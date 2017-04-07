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

        $itemId = helper::clearInt($itemId);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $items = new items($dbo);
        $items->setRequestFrom(auth::getCurrentUserId());

        $itemInfo = $items->info($itemId);

        if ($itemInfo['error'] === true || $itemInfo['removeAt'] != 0) {

            echo json_encode($result);
            exit;

        } else {

            $result = $items->like($itemId, auth::getCurrentUserId());

            if ($result['myLike']) {

                ob_start();

                ?>
                    <div class="fixed-action-btn" style="bottom: 65px; right: 24px;">
                        <a onclick="Items.removeFromFavorites('<?php echo $itemId; ?>'); return false;" class="btn-floating btn-large <?php echo SITE_THEME; ?>">
                            <i class="material-icons">grade</i>
                        </a>
                    </div>
                <?php

                $result['html'] = ob_get_clean();

            } else {

                ob_start();

                ?>
                    <div class="fixed-action-btn" style="bottom: 65px; right: 24px;">
                        <a onclick="Items.addToFavorites('<?php echo $itemId; ?>'); return false;" class="btn-floating btn-large <?php echo SITE_THEME; ?>">
                            <i class="material-icons">grade</i>
                        </a>
                    </div>
                <?php

                $result['html'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }
