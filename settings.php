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

    $accountId = auth::getCurrentUserId();

    $account = new account($dbo, $accountId);

    $error = false;
    $send_status = false;
    $fullname = "";

    if (auth::isSession()) {

        $ticket_email = "";
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $allowMessages = isset($_POST['allowMessages']) ? $_POST['allowMessages'] : '';

        $gender = isset($_POST['gender']) ? $_POST['gender'] : 0;

        $day = isset($_POST['day']) ? $_POST['day'] : 0;
        $month = isset($_POST['month']) ? $_POST['month'] : 0;
        $year = isset($_POST['year']) ? $_POST['year'] : 0;

        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';

        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $location = isset($_POST['location']) ? $_POST['location'] : '';
        $facebook_page = isset($_POST['facebook_page']) ? $_POST['facebook_page'] : '';
        $instagram_page = isset($_POST['instagram_page']) ? $_POST['instagram_page'] : '';

        $allowMessages = helper::clearText($allowMessages);
        $allowMessages = helper::escapeText($allowMessages);

        $gender = helper::clearInt($gender);

        $day = helper::clearInt($day);
        $month = helper::clearInt($month);
        $year = helper::clearInt($year);

        $fullname = helper::clearText($fullname);
        $fullname = helper::escapeText($fullname);

        $status = helper::clearText($status);
        $status = helper::escapeText($status);

        $location = helper::clearText($location);
        $location = helper::escapeText($location);

        $facebook_page = helper::clearText($facebook_page);
        $facebook_page = helper::escapeText($facebook_page);

        $instagram_page = helper::clearText($instagram_page);
        $instagram_page = helper::escapeText($instagram_page);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            if ($allowMessages === "on") {

                $account->setAllowMessages(1);

            } else {

                $account->setAllowMessages(0);
            }

            if (helper::isCorrectFullname($fullname)) {

                $account->edit($fullname);
            }

            if (helper::isCorrectPhone($phone)) {

                $account->setPhone($phone);
            }

            $account->setSex($gender);
            $account->setBirth($year, $month, $day);
            $account->setStatus($status);
            $account->setLocation($location);

            if (helper::isValidURL($facebook_page)) {

                $account->setFacebookPage($facebook_page);

            } else {

                $account->setFacebookPage("");
            }

            if (helper::isValidURL($instagram_page)) {

                $account->setInstagramPage($instagram_page);

            } else {

                $account->setInstagramPage("");
            }

            header("Location: /settings.php/?error=false");
            exit;
        }

        header("Location: /settings.php/?error=true");
        exit;
    }

    $account->setLastActive();

    $accountInfo = $account->get();

    auth::newAuthenticityToken();

    $page_id = "settings_profile";

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-settings']." | ".APP_TITLE;

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

                <h2 class="header"><?php echo $LANG['page-settings']; ?></h2>

                <div class="row msg-form">

                    <form class="" action="/settings.php" method="POST">

                        <?php

                        if ( isset($_GET['error']) ) {

                            switch ($_GET['error']) {

                                case "true" : {

                                    ?>

                                    <div class="input-field col s12">
                                        <div class="card red lighten-2">
                                            <div class="card-content white-text">
                                                <span class="card-title"><?php echo $LANG['msg-error-unknown']; ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <?php

                                    break;
                                }

                                default: {

                                    ?>

                                    <div class="input-field col s12">
                                        <div class="card teal lighten-2">
                                            <div class="card-content white-text">
                                                <span class="card-title"><?php echo $LANG['label-thanks']; ?></span>
                                                <br>
                                                <?php echo $LANG['label-settings-saved']; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php

                                    break;
                                }
                            }
                        }
                        ?>

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                        <p class="col s12">
                            <input type="checkbox" id="allowMessages" name="allowMessages" <?php if ($accountInfo['allowMessages'] == 1) echo "checked=\"checked\""; ?> />
                            <label for="allowMessages"><?php echo $LANG['label-messages-allow']; ?><br><?php echo $LANG['label-messages-allow-desc']; ?></label>
                        </p>

                        <div class="input-field col s12">
                            <input type="text" class="validate" id="phone" name="phone" value="<?php echo $accountInfo['phone']; ?>">
                            <label for="phone" class="active"><?php echo $LANG['label-phone']; ?></label>
                        </div>

                        <div class="input-field col s12">
                            <input type="text" class="validate" id="fullname" name="fullname" value="<?php echo $accountInfo['fullname']; ?>">
                            <label for="fullname" class="active"><?php echo $LANG['label-fullname']; ?></label>
                        </div>

                        <div class="input-field col s12">
                            <input type="text" class="validate" id="location" name="location" value="<?php echo $accountInfo['location']; ?>">
                            <label for="location" class="active"><?php echo $LANG['label-location']; ?></label>
                        </div>

                        <div class="input-field col s12">
                            <input type="text" class="validate" id="facebook_page" name="facebook_page" value="<?php echo $accountInfo['fb_page']; ?>">
                            <label for="facebook_page" class="active"><?php echo $LANG['label-facebook-link']; ?></label>
                        </div>

                        <div class="input-field col s12">
                            <input type="text" class="validate" id="instagram_page" name="instagram_page" value="<?php echo $accountInfo['instagram_page']; ?>">
                            <label for="instagram_page" class="active"><?php echo $LANG['label-instagram-link']; ?></label>
                        </div>

                        <div class="input-field col s12">
                            <textarea id="status" class="materialize-textarea" name="status" rows="10" cols="80"><?php echo $accountInfo['status']; ?></textarea>
                            <label for="status"><?php echo $LANG['label-status']; ?></label>

                            <script type="text/javascript">
                                $('#textarea1').trigger('autoresize');

                                $(document).ready(function() {

                                    $('select').material_select();
                                });
                            </script>

                        </div>

                        <div class="input-field col s12">
                            <select name="gender">
                                <option value="0" <?php if ($accountInfo['sex'] == SEX_FEMALE && $accountInfo['sex'] != SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-unknown']; ?></option>
                                <option value="1" <?php if ($accountInfo['sex'] == SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-male']; ?></option>
                                <option value="2" <?php if ($accountInfo['sex'] == SEX_FEMALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-female']; ?></option>
                            </select>
                            <label><?php echo $LANG['label-gender']; ?></label>
                        </div>

                        <div class="input-field col s12" style="padding-left: 0;">

                            <select name="day" class="col s4">
                                <?php

                                    for ($day = 1; $day <= 31; $day++) {

                                        if ($day == $accountInfo['day']) {

                                            echo "<option value=\"$day\" selected=\"selected\">$day</option>";

                                        } else {

                                            echo "<option value=\"$day\">$day</option>";
                                        }
                                    }
                                ?>
                            </select>

                            <select name="month" class="col s4">
                                <option value="0" <?php if ($accountInfo['month'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['month-jan']; ?></option>
                                <option value="1" <?php if ($accountInfo['month'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['month-feb']; ?></option>
                                <option value="2" <?php if ($accountInfo['month'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['month-mar']; ?></option>
                                <option value="3" <?php if ($accountInfo['month'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['month-apr']; ?></option>
                                <option value="4" <?php if ($accountInfo['month'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['month-may']; ?></option>
                                <option value="5" <?php if ($accountInfo['month'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['month-june']; ?></option>
                                <option value="6" <?php if ($accountInfo['month'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['month-july']; ?></option>
                                <option value="7" <?php if ($accountInfo['month'] == 7) echo "selected=\"selected\""; ?>><?php echo $LANG['month-aug']; ?></option>
                                <option value="8" <?php if ($accountInfo['month'] == 8) echo "selected=\"selected\""; ?>><?php echo $LANG['month-sept']; ?></option>
                                <option value="9" <?php if ($accountInfo['month'] == 9) echo "selected=\"selected\""; ?>><?php echo $LANG['month-oct']; ?></option>
                                <option value="10" <?php if ($accountInfo['month'] == 10) echo "selected=\"selected\""; ?>><?php echo $LANG['month-nov']; ?></option>
                                <option value="11" <?php if ($accountInfo['month'] == 11) echo "selected=\"selected\""; ?>><?php echo $LANG['month-dec']; ?></option>
                            </select>

                            <select name="year" class="col s4" style="padding-right: 0;">
                                <?php

                                    $current_year = date("Y");

                                    for ($year = 1915; $year <= $current_year; $year++) {

                                        if ($year == $accountInfo['year']) {

                                            echo "<option value=\"$year\" selected=\"selected\">$year</option>";

                                        } else {

                                            echo "<option value=\"$year\">$year</option>";
                                        }
                                    }
                                ?>
                            </select>

                            <label><?php echo $LANG['label-birth-date']; ?></label>

                        </div>

                        <div class="input-field col s12">
                            <button type="submit" class="btn waves-effect waves-light <?php echo SITE_THEME; ?> btn-large <?php echo SITE_THEME; ?>" name=""><?php echo $LANG['action-save']; ?></button>
                        </div>

                        <?php

                            if (FACEBOOK_AUTHORIZATION) {

                                ?>

                                    <div class="input-field col s12" style="margin-top: 60px;">
                                        <div class="card teal lighten-2">
                                            <div class="card-content white-text">
                                                <span class="card-title"><?php echo $LANG['label-services']; ?></span>
                                                <p><?php echo $LANG['action-connect-profile']; ?></p>
                                            </div>
                                            <div class="card-action">
                                                <a href="/settings/services"><?php echo $LANG['action-next']; ?></a>
                                            </div>
                                        </div>
                                    </div>

                                <?php
                            }
                        ?>

                        <div class="input-field col s12">
                            <div class="card teal lighten-2">
                                <div class="card-content white-text">
                                    <span class="card-title"><?php echo $LANG['label-password']; ?></span>
                                    <p><?php echo $LANG['action-change-password']; ?></p>
                                </div>
                                <div class="card-action">
                                    <a href="/settings/password"><?php echo $LANG['action-next']; ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="input-field col s12">
                            <div class="card teal lighten-2">
                                <div class="card-content white-text">
                                    <span class="card-title"><?php echo $LANG['label-profile']; ?></span>
                                    <p><?php echo $LANG['action-deactivation-profile']; ?></p>
                                </div>
                                <div class="card-action">
                                    <a href="/settings/deactivation"><?php echo $LANG['action-next']; ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="input-field col s12">
                            <div class="card teal lighten-2">
                                <div class="card-content white-text">
                                    <span class="card-title"><?php echo $LANG['label-blacklist']; ?></span>
                                    <p><?php echo $LANG['label-blacklist-desc']; ?></p>
                                </div>
                                <div class="card-action">
                                    <a href="/settings/blacklist"><?php echo $LANG['action-next']; ?></a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

	        </div>
        </div>
    </div>
</main>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
        ?>

</body>
</html>
