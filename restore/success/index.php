<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    $page_id = "restore_success";

    $css_files = array("my.css");
    $page_title = APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/header.inc.php");
?>

<body>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>
    <div class="section no-pad-bot" id="index-banner">
        <div class="container">

            <div class="row">
                <div class="col s12 m6" style="margin: 0 auto; float: none; margin-top: 100px;">
                    <div class="card teal lighten-2">
                        <div class="card-content white-text">
                            <span class="card-title"><strong>Success!</strong><br> A new password has been successfully installed!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/init.js"></script>

</body>
</html>
