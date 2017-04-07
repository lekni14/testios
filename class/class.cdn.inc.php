<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

class cdn extends db_connect
{
    private $ftp_url = "";
    private $ftp_server = "";
    private $ftp_user_name = "";
    private $ftp_user_pass = "";
    private $cdn_server = "";
    private $conn_id = false;

    public function __construct($dbo = NULL)
    {
        $this->conn_id = @ftp_connect($this->ftp_server);

        parent::__construct($dbo);
    }

    public function upload($file, $remote_file)
    {
        $remote_file = $this->cdn_server.$remote_file;

        if ($this->conn_id) {

            if (@ftp_login($this->conn_id, $this->ftp_user_name, $this->ftp_user_pass)) {

                // upload a file
                if (@ftp_put($this->conn_id, $remote_file, $file, FTP_BINARY)) {

                    return true;

                } else {

                    return false;
                }
            }
        }
    }

    public function uploadPhoto($imgFilename)
    {
        rename($imgFilename, "../../".PHOTO_PATH.basename($imgFilename));

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "fileUrl" => APP_URL."/".PHOTO_PATH.basename($imgFilename));

        return $result;
    }

    public function uploadCover($imgFilename)
    {
        rename($imgFilename, "../../".COVER_PATH.basename($imgFilename));

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "fileUrl" => APP_URL."/".COVER_PATH.basename($imgFilename));

        return $result;
    }

    public function uploadItemImg_API($imgFilename)
    {
        rename($imgFilename, "../../".ITEMS_PHOTO_PATH.basename($imgFilename));

        $result = array("error" => false,
            "error_code" => ERROR_SUCCESS,
            "fileUrl" => APP_URL."/".ITEMS_PHOTO_PATH.basename($imgFilename));

        return $result;
    }

    public function uploadItemImg($imgFilename)
    {
        rename($imgFilename, ITEMS_PHOTO_PATH.basename($imgFilename));

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "fileUrl" => APP_URL."/".ITEMS_PHOTO_PATH.basename($imgFilename));

        return $result;
    }

    public function uploadCategoryImg($imgFilename)
    {
        rename($imgFilename, CATEGORIES_PHOTO_PATH.basename($imgFilename));

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "fileUrl" => APP_URL."/".CATEGORIES_PHOTO_PATH.basename($imgFilename));

        return $result;
    }

    public function uploadChatImg($imgFilename)
    {
        rename($imgFilename, "../../".CHAT_IMAGE_PATH.basename($imgFilename));

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "fileUrl" => APP_URL."/".CHAT_IMAGE_PATH.basename($imgFilename));

        return $result;
    }
}
