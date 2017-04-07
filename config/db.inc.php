<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

$C = array();
$B = array();

$B['APP_DEMO'] = false;

$B['SITE_THEME'] = "marketplace-theme";                     //สีและสไตล์ ดูที่: https://kkdever.com เดี๋ยวทำธีมขาย
$B['SITE_TEXT_COLOR'] = "red-text text-darken-1";

$B['APP_MESSAGES_COUNTERS'] = true;
$B['APP_MYSQLI_EXTENSION'] = true;
$B['FACEBOOK_AUTHORIZATION'] = false;

// การตั้งค่า

$C['COMPANY_URL'] = "https://kkdever.com";
$B['APP_SUPPORT_EMAIL'] = "kkdever.app@gmail.com";
$B['APP_AUTHOR_PAGE'] = "Kkdever";
$B['APP_PATH'] = "app";
$B['APP_VERSION'] = "1";
$B['APP_AUTHOR'] = "Chatree Bamrung";
$B['APP_VENDOR'] = "kkdever.com";

// Paths ไฟล์รูป

$B['TEMP_PATH'] = "../tmp/";                                //ห้ามเปลี่ยน
$B['COVER_PATH'] = "../cover/";                             //ห้ามเปลี่ยน
$B['PHOTO_PATH'] = "../photo/";                             //ห้ามเปลี่ยน
$B['CHAT_IMAGE_PATH'] = "../chat_images/";                  //ห้ามเปลี่ยน
$B['ITEMS_PHOTO_PATH'] = "../items/";                       //ห้ามเปลี่ยน
$B['CATEGORIES_PHOTO_PATH'] = "../categories/";             //ห้ามเปลี่ยน

// copyright

$B['APP_NAME'] = "KK Engine v1";                            //
$B['APP_TITLE'] = "KK Engine v1";                           //
$B['APP_YEAR'] = "2017";                                    // Year in footer

// Link to GOOGLE Play App

$B['GOOGLE_PLAY_LINK'] = "https://play.google.com/store/apps/details?id=app.kkdever.phra";

// Your domain (host) and url!

$B['APP_HOST'] = "testios.birdday.me";
$B['APP_URL'] = "http://testios.birdday.me";

// Client ID. เขียนดัก ป้องกันไม่อนุญาตให้ใช้ API

$B['CLIENT_ID'] = 1;

// Google settings | For sending GCM (Google Cloud Messages)

$B['GOOGLE_API_KEY'] = "111111";		                        // FIREBASE Server Key
$B['GOOGLE_SENDER_ID'] = "111111";								          // FIREBASE SENDER ID

// Facebook settings

$B['FACEBOOK_APP_ID'] = "111111";
$B['FACEBOOK_APP_SECRET'] = "111111";

// SMTP Settings

$B['SMTP_HOST'] = 'mail.birdday.me';                        //SMTP host
$B['SMTP_AUTH'] = true;                                     //SMTP auth (Enable SMTP authentication)
$B['SMTP_SECURE'] = 'tls';                                  //SMTP secure (Enable TLS encryption, `ssl` also accepted)
$B['SMTP_PORT'] = 587;                                      //SMTP port
$B['SMTP_EMAIL'] = 'mail@birdday.me';                       //SMTP email
$B['SMTP_USERNAME'] = 'mail@birdday.me';                    //SMTP username
$B['SMTP_PASSWORD'] = '1111111';                            //SMTP password

//Database

$C['DB_HOST'] = "localhost:3306";                           //localhost or your db host
$C['DB_USER'] = "ounceomxu";                               //your db user
$C['DB_PASS'] = "_mdkcj&12ucd90";                          //your db password
$C['DB_NAME'] = "as_testios";                              //your db name


$C['ERROR_SUCCESS'] = 0;

$C['ERROR_UNKNOWN'] = 100;
$C['ERROR_ACCESS_TOKEN'] = 101;

$C['ERROR_LOGIN_TAKEN'] = 300;
$C['ERROR_EMAIL_TAKEN'] = 301;
$C['ERROR_FACEBOOK_ID_TAKEN'] = 302;
$C['ERROR_PHONE_TAKEN'] = 303;

$C['ERROR_ACCOUNT_ID'] = 400;

$C['DISABLE_LIKES_GCM'] = 0;
$C['ENABLE_LIKES_GCM'] = 1;

$C['DISABLE_COMMENTS_GCM'] = 0;
$C['ENABLE_COMMENTS_GCM'] = 1;

$C['DISABLE_FOLLOWERS_GCM'] = 0;
$C['ENABLE_FOLLOWERS_GCM'] = 1;

$C['DISABLE_MESSAGES_GCM'] = 0;
$C['ENABLE_MESSAGES_GCM'] = 1;

$C['DISABLE_GIFTS_GCM'] = 0;
$C['ENABLE_GIFTS_GCM'] = 1;

$C['SEX_UNKNOWN'] = 0;
$C['SEX_MALE'] = 1;
$C['SEX_FEMALE'] = 2;

$C['USER_CREATED_SUCCESSFULLY'] = 0;
$C['USER_CREATE_FAILED'] = 1;
$C['USER_ALREADY_EXISTED'] = 2;
$C['USER_BLOCKED'] = 3;
$C['USER_NOT_FOUND'] = 4;
$C['USER_LOGIN_SUCCESSFULLY'] = 5;
$C['EMPTY_DATA'] = 6;
$C['ERROR_API_KEY'] = 7;

$C['NOTIFY_TYPE_LIKE'] = 0;
$C['NOTIFY_TYPE_FOLLOWER'] = 1;
$C['NOTIFY_TYPE_MESSAGE'] = 2;
$C['NOTIFY_TYPE_COMMENT'] = 3;
$C['NOTIFY_TYPE_COMMENT_REPLY'] = 4;
$C['NOTIFY_TYPE_GIFT'] = 6;
$C['NOTIFY_TYPE_REVIEW'] = 7;

$C['GCM_NOTIFY_CONFIG'] = 0;
$C['GCM_NOTIFY_SYSTEM'] = 1;
$C['GCM_NOTIFY_CUSTOM'] = 2;
$C['GCM_NOTIFY_LIKE'] = 3;
$C['GCM_NOTIFY_ANSWER'] = 4;
$C['GCM_NOTIFY_QUESTION'] = 5;
$C['GCM_NOTIFY_COMMENT'] = 6;
$C['GCM_NOTIFY_FOLLOWER'] = 7;
$C['GCM_NOTIFY_PERSONAL'] = 8;
$C['GCM_NOTIFY_MESSAGE'] = 9;
$C['GCM_NOTIFY_COMMENT_REPLY'] = 10;
$C['GCM_NOTIFY_GIFT'] = 14;
$C['GCM_NOTIFY_REVIEW'] = 15;

$C['ACCOUNT_STATE_ENABLED'] = 0;
$C['ACCOUNT_STATE_DISABLED'] = 1;
$C['ACCOUNT_STATE_BLOCKED'] = 2;
$C['ACCOUNT_STATE_DEACTIVATED'] = 3;

$C['ACCOUNT_TYPE_USER'] = 0;
$C['ACCOUNT_TYPE_GROUP'] = 1;
$C['ACCOUNT_TYPE_PAGE'] = 2;

$C['ACCOUNT_ACCESS_LEVEL_AVAILABLE_TO_ALL'] = 0;
$C['ACCOUNT_ACCESS_LEVEL_AVAILABLE_TO_FRIENDS'] = 1;

$C['ADMIN_ACCESS_LEVEL_NULL'] = -1;
$C['ADMIN_ACCESS_LEVEL_FULL'] = 0;
$C['ADMIN_ACCESS_LEVEL_MODERATOR'] = 1;
$C['ADMIN_ACCESS_LEVEL_GUEST'] = 2;

// Languages

$LANGS = array();
$LANGS['English'] = "en";
$LANGS['ภาษาไทย'] = "th";
