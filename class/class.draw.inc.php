<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

class draw extends db_connect
{
	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    static function messageItem($message, $LANG, $helper)
    {
        $time = new language(NULL, $LANG['lang-code']);

        $message['message'] = helper::processMsgText($message['message']);

        ?>

        <li class="collection-item2 avatar2" data-id="<?php echo $message['id']; ?>">
            <!-- <a href="/profile-<?php echo $message['fromUserId']; ?>.html"><img src="<?php if (strlen($message['fromUserPhotoUrl']) != 0 ) { echo $message['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="" class="circle"></a> -->

						<?php
							if (!$message['fromUserOnline']) {
									?>
									<i class="left offline-badge"></i>
									<a href="/profile-<?php echo $message['fromUserId']; ?>.html">
										<?php
											if (!$message['fromUserVerify']) {
													?>
													<span class="left show-vip-no-badge"></span>
													<img class="circle" src="<?php if ( strlen($message['fromUserPhotoUrl']) != 0 ) { echo $message['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="">
													<?php
											} else {
													?>
													<span class="left show-vip-badge"></span>
													<img style="border:1px solid #ffba00;" class="circle" src="<?php if ( strlen($message['fromUserPhotoUrl']) != 0 ) { echo $message['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="">
													<?php
											}
										?>

									</a>
									<?php
							} else {
									?>
									<i class="left online-badge"></i>
									<a href="/profile-<?php echo $message['fromUserId']; ?>.html">
										<?php
											if (!$message['fromUserVerify']) {
													?>
													<span class="left show-vip-no-badge"></span>
													<img class="circle" src="<?php if ( strlen($message['fromUserPhotoUrl']) != 0 ) { echo $message['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="">
													<?php
											} else {
													?>
													<span class="left show-vip-badge"></span>
													<img style="border:1px solid #ffba00;" class="circle" src="<?php if ( strlen($message['fromUserPhotoUrl']) != 0 ) { echo $message['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="">
													<?php
											}
										?>
									</a>
									<?php
							}
						?>

						<div style="margin-left:55px;">
						<?php
							if (!$message['fromUserVerify']) {
									?>
									<span style="font-size:16px;" class="title chat-title"><?php echo $message['fromUserFullname']; ?></span>
									<?php
							} else {
									?>
									<span style="color: #ffba00; font-size:16px;" class="title chat-title"><?php echo $message['fromUserFullname']; ?></span>
									<?php
							}
						?>

            <p class="text-title" style="max-width:80%;">
                <?php

                if (strlen($message['message']) > 0) {

                    ?>
                        <?php echo $message['message']; ?>
                    <?php
                }

                if (strlen($message['imgUrl']) > 0) {

                    ?>
									</br><a href="<?php echo $message['imgUrl']; ?>" target="_blank"><img style="max-width: 50%; margin-top: 10px;" src="<?php echo $message['imgUrl']; ?>"></a></br>
                    <?php
                }

                ?>

            </p>
            <a href="javascript:void(0)" class="secondary-content2 date-text font-12" ><?php echo $time->timeAgo($message['createAt']); ?></a>
						</div>
        </li>

        <?php
    }

    static function commentItem($value, $LANG, $helper)
    {

        $value['comment'] = helper::processCommentText($value['comment']);

        ?>
            <li class="collection-item2 avatar2" data-id="<?php echo $value['id']; ?>">
                <!-- <a href="/profile-<?php echo $value['fromUserId']; ?>.html"><img src="<?php if ( strlen($value['fromUserPhotoUrl']) != 0 ) { echo $value['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="" class="circle"></a> -->

								<?php
									if (!$value['fromUserOnline']) {
											?>
											<i class="left offline-badge"></i>
											<a href="/profile-<?php echo $value['fromUserId']; ?>.html">
												<?php
													if (!$value['fromUserVerify']) {
															?>
															<span class="left show-vip-no-badge"></span>
															<img class="circle" src="<?php if ( strlen($value['fromUserPhotoUrl']) != 0 ) { echo $value['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="">
															<?php
													} else {
															?>
															<span class="left show-vip-badge"></span>
															<img style="border:1px solid #ffba00;" class="circle" src="<?php if ( strlen($value['fromUserPhotoUrl']) != 0 ) { echo $value['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="">
															<?php
													}
												?>

											</a>
											<?php
									} else {
											?>
											<i class="left online-badge"></i>
											<a href="/profile-<?php echo $value['fromUserId']; ?>.html">
												<?php
													if (!$value['fromUserVerify']) {
															?>
															<span class="left show-vip-no-badge"></span>
															<img class="circle" src="<?php if ( strlen($value['fromUserPhotoUrl']) != 0 ) { echo $value['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="">
															<?php
													} else {
															?>
															<span class="left show-vip-badge"></span>
															<img style="border:1px solid #ffba00;" class="circle" src="<?php if ( strlen($value['fromUserPhotoUrl']) != 0 ) { echo $value['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="">
															<?php
													}
												?>
											</a>
											<?php
									}
								?>

								<div style="margin-left:55px;">
								<?php
									if (!$value['fromUserVerify']) {
											?>
											<span style="font-size:16px;" class="title chat-title"><?php echo $value['fromUserFullname']; ?></span>
											<?php
									} else {
											?>
											<span style="color: #ffba00; font-size:16px;" class="title chat-title"><?php echo $value['fromUserFullname']; ?></span>
											<?php
									}
								?>

                <p style="max-width:90%;">
                    <?php
                    echo $value['comment'];
                    ?>
                    </p>
                    <p style="color: #6c6c6c; font-size: 14px">
                    <?php echo $value['timeAgo']; ?>
                    <p>

                <?php

                if ($value['fromUserId'] == auth::getCurrentUserId()) {

                    ?>
                    <a href="#!" onclick="Comments.remove('<?php echo $value['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="secondary-content2 <?php echo SITE_TEXT_COLOR; ?>"><i class="material-icons date-text md-16">delete</i></a>
                    <?php

                } else {

                    ?>
                    <a href="#!" onclick="Comments.reply('<?php echo $value['id']; ?>', '<?php echo $value['fromUserId']; ?>', '<?php echo $value['fromUserUsername']; ?>'); return false;" class="secondary-content2 <?php echo SITE_TEXT_COLOR; ?>"><i class="material-icons date-text md-16">reply</i></a>
                    <?php
                }
                ?>
							</div>
            </li>
        <?php
    }
}
