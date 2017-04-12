<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

class imglib extends db_connect
{
    private $profile_id = 0;
    private $request_from = 0;

    public function __construct($dbo = NULL, $profile_id = 0)
    {
        parent::__construct($dbo);
    }

    public function checkImg($img_filename, $min_width = 99, $min_height = 99)
    {
        $result = false;

        if (file_exists($img_filename)) {

            list($w, $h, $type) = getimagesize($img_filename);

            if ($type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {

                if ($w > $min_width && $h > $min_height) {

                    $result = true;
                }
            }
        }

        return $result;
    }

    public function createItemImg_API($imgFilename, $addon)
    {
        $result = array("error" => true,
                        "error_type" => "Unknown");

        $imgFilename_ext = pathinfo($addon, PATHINFO_EXTENSION);
        $imgNewName = helper::generateHash(10);

        $imgOrigin = "img_".$imgNewName.".".$imgFilename_ext;

        if ($this->checkImg($imgFilename)) {

            $result['asd'] = "asd";

            list($w, $h, $type) = getimagesize($imgFilename);

            if (copy($imgFilename, "../../".TEMP_PATH.$imgOrigin)) {

                $imgFilename = "../../".TEMP_PATH.$imgOrigin;
            }

            if ($type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {

                $this->img_resize($imgFilename, "../../".TEMP_PATH.$imgOrigin, 400, 0);

                $result['error'] = false;

            } else if ($type == IMAGETYPE_GIF) {

                $result['error'] = true;
                $result['error_type'] = "Gif";

            } else {

                $result['error'] = true;
                $result['error_type'] = "Unknown";
            }
        }

        if ($result['error'] === false) {

            $cdn = new cdn($this->db);

            $response = array();

            $response = $cdn->uploadItemImg_API("../../".TEMP_PATH.$imgOrigin);

            if ($response['error'] === false) {

                $result['imgUrl'] = $response['fileUrl'];
            }

            @unlink("../../".TEMP_PATH.$imgOrigin);
        }

        return $result;
    }

    public function createItemImg($imgFilename, $addon)
    {
        $result = array("error" => true,
                        "error_type" => "Unknown");

        $imgFilename_ext = pathinfo($addon, PATHINFO_EXTENSION);
        $imgNewName = helper::generateHash(10);

        $imgOrigin = "img_".$imgNewName.".".$imgFilename_ext;

        if ($this->checkImg($imgFilename)) {

            $result['asd'] = "asd";

            list($w, $h, $type) = getimagesize($imgFilename);

            if (copy($imgFilename, TEMP_PATH.$imgOrigin)) {

                $imgFilename = TEMP_PATH.$imgOrigin;
            }

            if ($type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {

                $this->img_resize($imgFilename, TEMP_PATH.$imgOrigin, 400, 0);

                $result['error'] = false;

            } else if ($type == IMAGETYPE_GIF) {

                $result['error'] = true;
                $result['error_type'] = "Gif";

            } else {

                $result['error'] = true;
                $result['error_type'] = "Unknown";
            }
        }

        if ($result['error'] === false) {

            $cdn = new cdn($this->db);

            $response = array();

            $response = $cdn->uploadItemImg(TEMP_PATH.$imgOrigin);

            if ($response['error'] === false) {

                $result['imgUrl'] = $response['fileUrl'];
            }

            @unlink(TEMP_PATH.$imgOrigin);
        }
        $result['imgName'] = $imgOrigin;
        return $result;
    }

    public function createCategoryImg($imgFilename, $addon)
    {
        $result = array("error" => true,
                        "error_type" => "Unknown");

        $imgFilename_ext = pathinfo($addon, PATHINFO_EXTENSION);
        $imgNewName = helper::generateHash(10);

        $imgOrigin = "img_".$imgNewName.".".$imgFilename_ext;

        if ($this->checkImg($imgFilename)) {

            $result['asd'] = "asd";

            list($w, $h, $type) = getimagesize($imgFilename);

            if (copy($imgFilename, TEMP_PATH.$imgOrigin)) {

                $imgFilename = TEMP_PATH.$imgOrigin;
            }

            if ($type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {

                $this->img_resize($imgFilename, TEMP_PATH.$imgOrigin, 512, 0);

                $result['error'] = false;

            } else if ($type == IMAGETYPE_GIF) {

                $result['error'] = true;
                $result['error_type'] = "Gif";

            } else {

                $result['error'] = true;
                $result['error_type'] = "Unknown";
            }
        }

        if ($result['error'] === false) {

            $cdn = new cdn($this->db);

            $response = array();

            $response = $cdn->uploadCategoryImg(TEMP_PATH.$imgOrigin);

            if ($response['error'] === false) {

                $result['imgUrl'] = $response['fileUrl'];
            }
            @unlink(TEMP_PATH.$imgOrigin);
        }
        return $result;
    }

    public function createChatImg($imgFilename, $addon)
    {
        $result = array("error" => true);

        $imgFilename_ext = pathinfo($addon, PATHINFO_EXTENSION);
        $imgNewName = helper::generateHash(10);

        $imgOrigin = "img_".$imgNewName.".".$imgFilename_ext;

        if ($this->checkImg($imgFilename)) {

            list($w, $h, $type) = getimagesize($imgFilename);

            if (copy($imgFilename, "../../".TEMP_PATH.$imgOrigin)) {

                $imgFilename = "../../".TEMP_PATH.$imgOrigin;
            }

            if ($type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {

                $this->img_resize($imgFilename, "../../".TEMP_PATH . $imgOrigin, 800, 0);

                $result['error'] = false;

            } else if ($type == IMAGETYPE_GIF) {

                $result['error'] = false;

            } else {

                $result['error'] = true;
            }
        }

        if ($result['error'] === false) {

            $cdn = new cdn($this->db);

            $response = array();

            $response = $cdn->uploadChatImg("../../".TEMP_PATH.$imgOrigin);

            if ($response['error'] === false) {

                $result['imgUrl'] = $response['fileUrl'];
            }

            @unlink("../../".TEMP_PATH.$imgOrigin);
        }

        return $result;
    }

    public function createCover($imgFilename, $addon)
    {
        $result = array("error" => true);

        $imgFilename_ext = pathinfo($addon, PATHINFO_EXTENSION);
        $imgNewName = helper::generateHash(7);

        $imgOrigin = "origin_c_".$imgNewName.".".$imgFilename_ext;
        $imgNormal = "normal_c_".$imgNewName.".".$imgFilename_ext;

        if ($this->checkImg($imgFilename)) {

            list($w, $h, $type) = getimagesize($imgFilename);

            if (rename($imgFilename, "../../".TEMP_PATH.$imgOrigin)) {

                $imgFilename = "../../".TEMP_PATH.$imgOrigin;
            }

            if ($type == IMAGETYPE_JPEG) {

                $this->img_resize($imgFilename, "../../".TEMP_PATH.$imgNormal, 800, 0);

                $result['error'] = false;

            } elseif ($type == IMAGETYPE_PNG) {

                //PNG

                $this->img_resize($imgFilename, "../../".TEMP_PATH.$imgNormal, 800, 0);

                $result['error'] = false;

            } else {

                $result['error'] = true;
            }
        }

        if ($result['error'] === false) {

            $cdn = new cdn($this->db);

            $response = array();

            $response = $cdn->uploadCover("../../".TEMP_PATH . $imgNormal);

            if ($response['error'] === false) {

                $result['normalCoverUrl'] = $response['fileUrl'];
                $result['originCoverUrl'] = $response['fileUrl'];
            }

            @unlink("../../".TEMP_PATH.$imgOrigin);
            @unlink("../../".TEMP_PATH.$imgNormal);
        }

        return $result;
    }

    public function createPhoto($imgFilename)
    {
        $result = array("error" => true);

        $imgFilename_ext = pathinfo($imgFilename, PATHINFO_EXTENSION);
        $imgNewName = helper::generateHash(7);

        $imgOrigin = "origin_".$imgNewName.".".$imgFilename_ext;
        $imgNormal = "normal_".$imgNewName.".".$imgFilename_ext;
        $imgThumbLow = "thumb_low_".$imgNewName.".".$imgFilename_ext;
        $imgThumbBig = "thumb_big_".$imgNewName.".".$imgFilename_ext;

        if ($this->checkImg($imgFilename)) {

            list($w, $h, $type) = getimagesize($imgFilename);

            if (rename($imgFilename, "../../".TEMP_PATH.$imgOrigin)) {

                $imgFilename = "../../".TEMP_PATH.$imgOrigin;
            }

            if ($type == IMAGETYPE_JPEG) {

                $this->img_resize($imgFilename, "../../".TEMP_PATH.$imgNormal, 800, 0);

                $photo = new photo($this->db, $imgFilename, 512);
                imagejpeg($photo->getImgData(), "../../".TEMP_PATH.$imgThumbBig, 100);
                unset($photo);

                $photo = new photo($this->db, $imgFilename, 256);
                imagejpeg($photo->getImgData(), "../../".TEMP_PATH.$imgThumbLow, 100);
                unset($photo);

                $result['error'] = false;

            } elseif ($type == IMAGETYPE_PNG) {

                //PNG

                $this->img_resize($imgFilename, "../../".TEMP_PATH.$imgNormal, 800, 0);

                $photo = new photo($this->db, $imgFilename, 512);
                imagepng($photo->getImgData(), "../../".TEMP_PATH.$imgThumbBig);
                unset($photo);

                $photo = new photo($this->db, $imgFilename, 256);
                imagepng($photo->getImgData(), "../../".TEMP_PATH.$imgThumbLow);
                unset($photo);

                $result['error'] = false;

            } else {

                $result['error'] = true;
            }
        }

        if ($result['error'] === false) {

            $cdn = new cdn($this->db);

            $response = array();

            $response = $cdn->uploadPhoto("../../".TEMP_PATH.$imgNormal);

            if ($response['error'] === false) {

                $result['normalPhotoUrl'] = $response['fileUrl'];
                $result['originPhotoUrl'] = $response['fileUrl'];
            }

            $response = $cdn->uploadPhoto("../../".TEMP_PATH.$imgThumbBig);

            if ($response['error'] === false) {

                $result['bigPhotoUrl'] = $response['fileUrl'];
            }

            $response = $cdn->uploadPhoto("../../".TEMP_PATH.$imgThumbLow);

            if ($response['error'] === false) {

                $result['lowPhotoUrl'] = $response['fileUrl'];
            }

            @unlink("../../".TEMP_PATH.$imgOrigin);
            @unlink("../../".TEMP_PATH.$imgNormal);
            @unlink("../../".TEMP_PATH.$imgThumbBig);
            @unlink("../../".TEMP_PATH.$imgThumbLow);
        }

        return $result;
    }

    /***********************************************************************************
    ตัวบีบอัดไฟล์
     ***********************************************************************************/
    public function img_resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 90)
    {

        if (!file_exists($src)) return false;

        $size = getimagesize($src);

        if ($size === false) return false;

        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
        $icfunc = 'imagecreatefrom'.$format;

        if (!function_exists($icfunc)) return false;

        $x_ratio = $width  / $size[0];
        $y_ratio = $height / $size[1];

        if ($height == 0) {

            $y_ratio = $x_ratio;
            $height  = $y_ratio * $size[1];

        } elseif ($width == 0) {

            $x_ratio = $y_ratio;
            $width   = $x_ratio * $size[0];
        }

        $ratio       = min($x_ratio, $y_ratio);
        $use_x_ratio = ($x_ratio == $ratio);

        $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
        $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
        $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width)   / 2);
        $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

        // если не нужно увеличивать маленькую картинку до указанного размера
        if ($size[0] < $new_width && $size[1] < $new_height) {

            $width = $new_width = $size[0];
            $height = $new_height = $size[1];
        }

        $isrc  = $icfunc($src);
        $idest = imagecreatetruecolor($width, $height);

        imagefill($idest, 0, 0, $rgb);
        imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);

        $i = strrpos($dest,'.');
        if (!$i) return '';
        $l = strlen($dest) - $i;
        $ext = substr($dest,$i+1,$l);

        switch ($ext) {

            case 'jpeg':
            case 'jpg':
                imagejpeg($idest, $dest, $quality);
                break;
            case 'gif':
                imagegif($idest, $dest);
                break;
            case 'png':
                imagepng($idest, $dest);
                break;
        }

        imagedestroy($isrc);
        imagedestroy($idest);

        return true;
    }
}
