<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

	class language extends db_connect
    {

        private $language;

		public function __construct($dbo = NULL, $language = "th")
        {

			parent::__construct($dbo);

            $this->set($language);

		}

        public function timeAgo($time)
        {

            switch($this->get()) {

                case "th" :  {

                    $titles = array(" นาที"," นาที"," นาที");
                    $titles2 = array(" ชั่วโมง"," ชั่วโมง"," ชั่วโมง");
                    $titles3 = array(" วัน"," วัน"," วัน");
                    $titles4 = array(" เดือน"," เดือน"," เดือน");
                    $about = " ผ่านมา";
                    $now = "เมื่อสักครู่นี้";
                    break;
                }
                
                default :  {

//                    $titles = array("minute","minutes","minutes");
//                    $titles2 = array("hour","hours","hours");
//                    $titles3 = array("day","days","days");
//                    $titles4 = array("month","months","months");
//                    $about = " ago";
//                    $now = "less than a minute ago";

                    $titles = array(" นาที"," นาที"," นาที");
                    $titles2 = array(" ชั่วโมง"," ชั่วโมง"," ชั่วโมง");
                    $titles3 = array(" วัน"," วัน"," วัน");
                    $titles4 = array(" เดือน"," เดือน"," เดือน");
                    $about = "ที่แล้ว";
                    $now = "เมื่อสักครู่นี้";

                    break;
                }
            }

            $new_time = time();
            $time = $new_time - $time;

            if($time < 60) return $now; else
            if($time < 3600) return language::declOfNum(($time-($time%60))/60, $titles).$about; else
            if($time < 86400) return language::declOfNum(($time-($time%3600))/3600, $titles2).$about; else
            if($time < 2073600) return language::declOfNum(($time - ($time % 86400)) / 86400, $titles3).$about; else
            if($time < 62208000) return language::declOfNum(($time - ($time % 2073600)) / 2073600, $titles4).$about; else return gmdate("d-m-Y", $time);
        }

        static function declOfNum($number, $titles)
        {
            $cases = array(2, 0, 1, 1, 1, 2);
            return $number.''.$titles[ ($number%100>4 && $number%100<20) ? 2 : $cases[($number%10<5) ? $number%10:5] ];
        }

        static function getLikes($LANG, $result)
        {

            if ( $result['myLike'] && $result['likesCount'] == 1 ) {

                //Вам это понравилось

                return $LANG['label-mylike']." ".$LANG['label-like'];

            } else if ( $result['myLike'] && $result['likesCount'] == 2 ) {

                //Вам и 1 другому это понравилось

                return $LANG['label-mylike']." ".$LANG['label-and']." <a href=\"/{$result['fromUserUsername']}/post/{$result['id']}/people\">1 ".$LANG['label-mylike-user']."</a> ".$LANG['label-like'];

            } else if ( $result['myLike'] && $result['likesCount'] > 2 ) {

                //Вам и 2 другим это понравилось

                return $LANG['label-mylike']." ".$LANG['label-and']." <a href=\"/{$result['fromUserUsername']}/post/{$result['id']}/people\">".--$result['likesCount']." ".$LANG['label-mylike-peoples']."</a> ".$LANG['label-like'];

            } else if ( !$result['myLike'] && $result['likesCount'] > 1 ) {

                //6 пользователям это понравилось

                return "<a href=\"/{$result['fromUserUsername']}/post/{$result['id']}/people\">".$result['likesCount']." ".$LANG['label-like-peoples']."</a> ".$LANG['label-like'];

            } else if (!$result['myLike'] && $result['likesCount'] == 1) {

                //1 пользователю это понравилось

                return "<a href=\"/{$result['fromUserUsername']}/post/{$result['id']}/people\">1 ".$LANG['label-like-user']."</a> ".$LANG['label-like'];

            } else {

                return "";
            }
        }

        public function set($language)
        {

            $this->language = $language;
        }

        public function get()
        {

            return $this->language;
        }
	}

?>