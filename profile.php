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
        exit;
    }

    if (isset($_GET['id'])) {

        $profile_id = isset($_GET['id']) ? $_GET['id'] : 0;

        $profile_id = helper::clearInt($profile_id);

        $profile = new profile($dbo, $profile_id);

        $profile->setRequestFrom(auth::getCurrentUserId());
        $profileInfo = $profile->get();

    } else {

        header("Location: /");
        exit;
    }

    if ($profileInfo['error'] === true) {

        header("Location: /");
        exit;
    }

    if ($profileInfo['id'] == auth::getCurrentUserId()) {

        $page_id = "my-profile";

        $account = new account($dbo, $profileInfo['id']);
        $account->setLastActive();
        unset($account);

    } else {

        $page_id = "profile";
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("stubs/profile.php");
        exit;
    }

    $stream = new stream($dbo);
    $stream->setRequestFrom(auth::getCurrentUserId());

    $items_all = $stream->getAllCountByUserId($profileInfo['id']);
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $stream->getByUserId($itemId, $profileInfo['id']);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <div class="row more_cont">
                    <div class="col s12">
                        <a href="javascript:void(0)" onclick="Profile.moreItems('<?php echo $result['itemId']; ?>'); return false;">
                            <button class="btn waves-effect waves-light <?php echo SITE_THEME; ?> more_link"><?php echo $LANG['action-more']; ?></button>
                        </a>
                    </div>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $css_files = array("my.css", "account.css");
    $page_title = $profileInfo['fullname']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");

?>

<body>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>

<main class="content">

    <div class="container">
        <div class="row">
            <div class="col s12 m12 l12">

                <div class="row">
                    <div class="col s12">
                        <div class="card white">

                            <ul class="collection" style="border: none; border-bottom: 1px solid #e0e0e0">

                                <li class="collection-item avatar" style="padding-left: 94px">
                                    <img style="height: 64px; width: 64px;" src="<?php if ( strlen($profileInfo['bigPhotoUrl']) != 0 ) { echo $profileInfo['bigPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="" class="circle profile-img">
                                    <span style="font-size: 1.44rem;" class="title"><?php echo $profileInfo['fullname']; ?></span>
                                    <p>
                                        <span>@<?php echo $profileInfo['username']; ?></span>
                                        <br>

                                        <?php

                                            if ($profileInfo['online']) {

                                                echo "<span class=\"teal-text\">Online</span>";

                                            } else {

                                                if ($profileInfo['lastAuthorize'] == 0) {

                                                    echo "Offline";

                                                } else {

                                                    echo $profileInfo['lastAuthorizeTimeAgo'];
                                                }
                                            }
                                        ?>

                                    <br>
                                    </p>

                                    <?php

                                        if ($profileInfo['id'] == auth::getCurrentUserId()) {

                                            ?>
                                                <a href="/settings.php" class="secondary-content <?php echo SITE_TEXT_COLOR; ?>"><i class="material-icons">mode edit</i></a>
                                            <?php
                                        }

                                    ?>

                                </li>
                            </ul>

                            <div class="row">
                                <div class="col s12 m12 l12 left">

                                <div class="col s12  m7">
                                    <div class="card white" style="box-shadow: none">

                                    <ul class="collection" style="border: none;">

                                        <li class="collection-item">
                                            <h5 class="title"><?php echo $LANG['label-join-date']; ?></h5>
                                            <p><?php echo $profileInfo['createDate'];; ?></p>
                                        </li>

                                        <?php

                                            if ($profileInfo['sex'] != SEX_UNKNOWN) {

                                                ?>

                                                <li class="collection-item">
                                                    <h5 class="title"><?php echo $LANG['label-gender']; ?></h5>
                                                    <p><?php if ($profileInfo['sex'] == SEX_MALE) { echo $LANG['gender-male']; } else echo $LANG['gender-female']; ?></p>
                                                </li>

                                                <?php
                                            }

                                        ?>

                                        <?php

                                            if (strlen($profileInfo['location']) > 0) {

                                                ?>

                                                    <li class="collection-item">
                                                        <h5 class="title"><?php echo $LANG['label-location']; ?></h5>
                                                        <p><?php echo $profileInfo['location']; ?></p>
                                                    </li>

                                                <?php
                                            }

                                        ?>

                                        <?php

                                            if (strlen($profileInfo['fb_page']) > 0) {

                                                ?>

                                                <li class="collection-item">
                                                    <h5 class="title"><?php echo $LANG['label-facebook-link']; ?></h5>
                                                    <a href="<?php echo $profileInfo['fb_page']; ?>"><?php echo $profileInfo['fb_page']; ?></a>
                                                </li>

                                                <?php
                                            }

                                        ?>

                                        <?php

                                            if (strlen($profileInfo['instagram_page']) > 0) {

                                                ?>

                                                <li class="collection-item">
                                                    <h5 class="title"><?php echo $LANG['label-instagram-link']; ?></h5>
                                                    <a href="<?php echo $profileInfo['instagram_page']; ?>"><?php echo $profileInfo['instagram_page']; ?></a>
                                                </li>

                                                <?php
                                            }

                                        ?>

                                        <?php

                                            if (strlen($profileInfo['status']) > 0) {

                                                ?>

                                                    <li class="collection-item">
                                                        <h5 class="title"><?php echo $LANG['label-status']; ?></h5>
                                                        <p><?php echo $profileInfo['status']; ?></p>
                                                    </li>

                                                <?php
                                            }

                                        ?>

                                    </ul>

                                        <div class="row col s12 wall_cont">

                                            <h4 class="header">
                                                <?php echo $LANG['label-items']; ?>

                                                <?php

                                                    if ($items_all > 0) echo " ({$items_all})";
                                                ?>
                                            </h4>

                                            <?php

                                                $result = $stream->getByUserId(0, $profileInfo['id']);

                                                $items_loaded = count($result['items']);

                                                if ($items_loaded != 0) {

                                                    foreach ($result['items'] as $key => $value) {

                                                        draw($value, $LANG, $helper);
                                                    }

                                                    if ($items_all > 20) {

                                                        ?>

                                                        <div class="row more_cont">
                                                            <div class="col s12">
                                                                <a href="javascript:void(0)" onclick="Profile.moreItems('<?php echo $result['itemId']; ?>'); return false;">
                                                                    <button class="btn waves-effect waves-light <?php echo SITE_THEME; ?> more_link"><?php echo $LANG['action-more']; ?></button>
                                                                </a>
                                                            </div>
                                                        </div>

                                                    <?php
                                                    }

                                                } else {

                                                    ?>

                                                        <div class="row">
                                                            <div class="col s12">
                                                                <div class="card blue-grey darken-1">
                                                                    <div class="card-content white-text">
                                                                        <span class="card-title"><?php echo $LANG['label-empty-list']; ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <?php
                                                }
                                            ?>
                                        </div>

                                    </div>
                                </div>

                                <?php

                                    if ($profileInfo['id'] != auth::getCurrentUserId()) {

                                        ?>

                                        <div class="col s12  m5">
                                            <div class="card white" style="box-shadow: none">

                                                <div class="row">
                                                    <div class="input-field col s12 report_button_container" style="margin-top: 0">

                                                        <a onclick="Profile.getReportBox(); return false;" style="width: 100%" class="btn waves-effect waves-light <?php echo SITE_THEME; ?>"><?php echo $LANG['action-report']; ?></a>

                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="input-field col s12 block_button_container" style="margin-top: 0">

                                                        <?php

                                                        if ($profileInfo['blocked']) {

                                                            ?>
                                                                <a onclick="Profile.unblock('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" style="width: 100%" class="btn waves-effect waves-light <?php echo SITE_THEME; ?>"><?php echo $LANG['action-unblock']; ?></a>
                                                            <?php

                                                        } else {

                                                            ?>
                                                                <a onclick="Profile.block('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" style="width: 100%" class="btn waves-effect waves-light <?php echo SITE_THEME; ?>"><?php echo $LANG['action-block']; ?></a>
                                                            <?php
                                                        }
                                                        ?>

                                                    </div>
                                                </div>

                                                <?php

                                                    if ($profileInfo['allowMessages'] == 1) {

                                                        ?>

                                                            <div class="row">
                                                                <div class="input-field col s12" style="margin-top: 0">
                                                                    <a href="/chat.php/?chat_id=0&user_id=<?php echo $profileInfo['id']; ?>" style="width: 100%; min-height: 36px; height: auto" class="btn waves-effect waves-light <?php echo SITE_THEME; ?>"><?php echo $LANG['action-send-message']; ?></a>
                                                                </div>
                                                            </div>

                                                        <?php
                                                    }
                                                ?>

                                            </div>
                                        </div>

                                        <?php

                                    } else {

                                        ?>

                                            <div class="col s12  m5">
                                                <div class="card white" style="box-shadow: none">

                                                    <div class="row">
                                                        <div class="input-field col s12" style="margin-top: 0">
                                                            <a onclick="Profile.changePhoto(); return false;" style="width: 100%; min-height: 36px; height: auto" class="btn waves-effect waves-light <?php echo SITE_THEME; ?>"><?php echo $LANG['action-change-photo']; ?></a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="fixed-action-btn" style="bottom: 65px; right: 24px;">
                                                <a href="/profile/new_item.php" class="btn-floating btn-large <?php echo SITE_THEME; ?>">
                                                    <i class="material-icons">add</i>
                                                </a>
                                            </div>

                                        <?php
                                    }
                                ?>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

	        </div>
        </div>
    </div>
</main>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
        ?>

        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

        <script type="text/javascript">

            window.Friends || ( window.Friends = {} );

            Friends.remove = function (friend_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/friends/method/remove.php',
                    data: 'friend_id=' + friend_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("div.friends_button_container").html(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

            window.Profile || ( window.Profile = {} );

            Profile.moreItems = function (offset) {

                $.ajax({
                    type: 'POST',
                    url: '/profile.php/?id=' + <?php echo $profileInfo['id']; ?>,
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.more_cont').remove();

                        if (response.hasOwnProperty('html')){

                            $("div.wall_cont").append(response.html);
                        }

                        items_loaded = response.items_loaded;
                        items_all = response.items_all;
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Profile.changePhoto = function() {

                $('#img-box').openModal();
            };

            Profile.getReportBox = function() {

                $('#report-box').openModal();
            };

            Profile.sendReport = function (profile_id, reason, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/report.php',
                    data: 'profile_id=' + profile_id + "&reason=" + reason + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {

                        $('#report-box').closeModal();

                        if (response.hasOwnProperty('error')) {

                            Materialize.toast('<?php echo $LANG['label-profile-reported']; ?>', 3000);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Profile.block = function (profile_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/block.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("div.block_button_container").html(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Profile.unblock = function (profile_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/unblock.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("div.block_button_container").html(response.html);
                        }
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

    <div id="img-box" class="modal">
        <div class="modal-content">
            <h4><?php echo $LANG['label-image-upload-description']; ?></h4>
            <div class="file_select_btn_container">
                <div class="file_select_btn btn <?php echo SITE_THEME; ?>" style="width: 220px"><?php echo $LANG['action-add-img']; ?></div>
            </div>

            <div class="file_select_btn_description" style="display: none">
                <?php echo $LANG['msg-loading']; ?>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class=" modal-action modal-close waves-effect waves-ripple btn-flat"><?php echo $LANG['action-close']; ?></a>
        </div>
    </div>

    <?php

        if (auth::getCurrentUserId() != $profileInfo['id']) {

            ?>

                <div id="report-box" class="modal">
                    <div class="modal-content">
                        <h5><?php echo $LANG['page-profile-report-sub-title']; ?></h5>
                        <a onclick="Profile.sendReport('<?php echo $profileInfo['id']; ?>', '0', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-ripple btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-1']; ?></a>
                        <a onclick="Profile.sendReport('<?php echo $profileInfo['id']; ?>', '1', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-ripple btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-2']; ?></a>
                        <a onclick="Profile.sendReport('<?php echo $profileInfo['id']; ?>', '2', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-ripple btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-3']; ?></a>
                        <a onclick="Profile.sendReport('<?php echo $profileInfo['id']; ?>', '3', '<?php echo auth::getAccessToken(); ?>'); return false;" class="waves-effect waves-ripple btn-flat" style="display: block" href="javascript:void(0)"><?php echo $LANG['label-profile-report-reason-4']; ?></a>
                    </div>
                    <div class="modal-footer">
                        <a href="#!" class=" modal-action modal-close waves-effect waves-ripple btn-flat"><?php echo $LANG['action-cancel']; ?></a>
                    </div>
                </div>

            <?php
        }
    ?>

    <script type="text/javascript">

        $('.file_select_btn').upload({
            name: 'uploaded_file',
            method: 'post',
            enctype: 'multipart/form-data',
            action: '/ajax/profile/method/uploadPhoto.php',
            onComplete: function(text) {

                var response = JSON.parse(text);

                if (response.hasOwnProperty('error')) {

                    if (response.error === false) {

                        $('#img-box').closeModal();

                        if (response.hasOwnProperty('lowPhotoUrl')) {

                            $("img.profile-img").attr("src", response.lowPhotoUrl);
                        }
                    }
                }

                $("div.file_select_btn_description").hide();
                $("div.file_select_btn_container").show();
            },
            onSubmit: function() {

                $("div.file_select_btn_container").hide();
                $("div.file_select_btn_description").show();
            }
        });

    </script>

</body>
</html>

<?php

    function draw($item, $LANG, $helper)
    {
        ?>
                <div class="col s12 m12 item" data-id="<?php echo $item['id']; ?>">
                    <a href="/view_item.php/?id=<?php echo $item['id']; ?>">
                        <div class="card">
                            <div class="card-image">
                                <img src="<?php echo $item['imgUrl']; ?>">
                                <span class="card-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis"><?php echo $item['categoryTitle']; ?></span>
                            </div>
                            <div class="card-content">
                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis"><?php echo $item['itemTitle']; ?></p>
                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis"><?php echo $item['price']; ?><?php echo $LANG['label-currency']; ?></p>
                            </div>
                        </div>
                    </a>
                </div>

        <?php
    }
