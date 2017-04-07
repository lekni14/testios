<?php

/* KK Engine v1.1.0
 *
 * https://kkdever.com
 * birdcpeneu@gmail.com
 *
 * Copyright 2016-2017 https://kkdever.com
 */

    class update extends db_connect
    {
        public function __construct($dbo = NULL)
        {
            parent::__construct($dbo);

        }

        function setCommentEmojiSupport()
        {
            $stmt = $this->db->prepare("ALTER TABLE comments charset = utf8mb4, MODIFY COLUMN comment VARCHAR(800) CHARACTER SET utf8mb4");
            $stmt->execute();
        }

        function setChatEmojiSupport()
        {
            $stmt = $this->db->prepare("ALTER TABLE messages charset = utf8mb4, MODIFY COLUMN message VARCHAR(800) CHARACTER SET utf8mb4");
            $stmt->execute();
        }

        function setDialogsEmojiSupport()
        {
            $stmt = $this->db->prepare("ALTER TABLE chats charset = utf8mb4, MODIFY COLUMN message VARCHAR(800) CHARACTER SET utf8mb4");
            $stmt->execute();
        }

        function addColumnToUsersTable1()
        {
            $stmt = $this->db->prepare("ALTER TABLE users ADD ios_fcm_regid TEXT after gcm_regid");
            $stmt->execute();
        }
    }
