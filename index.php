<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (auth::isSession()) {

        header("Location: /stream.php");
    }

    $css_files = array("my.css");
    $page_title = APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/header.inc.php");
?>

<body>

<?php

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
?>

<div class="section no-pad-bot" id="index-banner">

    <div class="container" style="margin-top: 140px; margin-bottom: 140px;">
        <br><br>
        <h1 class="header center orange-text"><?php echo APP_NAME; ?></h1>

        <div class="row center">
            <h5 class="header col s12 light">ดาวน์โหลดแอป <?php echo APP_NAME; ?> now!</h5>
        </div>

        <div class="row center">
            <a href="<?php echo GOOGLE_PLAY_LINK; ?>">
                <button class="btn-large waves-effect waves-light teal">โหลดได้ที่ <?php echo APP_NAME; ?> Google Play<i class="material-icons right">file_download</i></button>
            </a>
        </div>

        <br><br>
    </div>

</div>


<footer class="page-footer white" style="padding-top: 0px;">
    <div class="footer-copyright white">
        <div class="container <?php echo SITE_TEXT_COLOR; ?>">
            <span class="grey-text darken-2"><?php echo APP_TITLE; ?> © <?php echo APP_YEAR; ?></span>
            <span style="margin-left: 10px;"><a class="text-lighten-4 modal-trigger <?php echo SITE_TEXT_COLOR; ?> text-darken-3" href="#lang-box"><?php echo $LANG['lang-name']; ?></a></span>
            <span class="right"><a class="text-lighten-4 <?php echo SITE_TEXT_COLOR; ?>" target="_blank" href="<?php echo COMPANY_URL; ?>"><?php echo APP_VENDOR; ?></a></span>
        </div>
    </div>
</footer>

<div id="lang-box" class="modal">
    <div class="modal-content">
        <h4><?php echo $LANG['page-language']; ?></h4>
        <?php

        foreach ($LANGS as $name => $val) {

            echo "<a onclick=\"App.setLanguage('$val'); return false;\" class=\"waves-effect btn-flat\" style=\"display: block\" href=\"javascript:void(0)\">$name</a>";
        }

        ?>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect btn-flat"><?php echo $LANG['action-close']; ?></a>
    </div>
</div>

    <script type="text/javascript" src="/js/materialize.min.js"></script>

    <script src="/js/init.js"></script>

<script type="text/javascript">

    $('.modal-trigger').leanModal({
                dismissible: true, // Modal can be dismissed by clicking outside of the modal
                opacity: .5, // Opacity of modal background
                in_duration: 300, // Transition in duration
                out_duration: 200, // Transition out duration
                ready: function() {  }, // Callback for Modal open
                complete: function() { } // Callback for Modal close
        }
    );

    window.App || ( window.App = {} );

    App.setLanguage = function(language) {

        $.cookie("lang", language, { expires : 7, path: '/' });
        $('#lang-box').closeModal();
        location.reload();
    };

</script>

</body>
</html>
