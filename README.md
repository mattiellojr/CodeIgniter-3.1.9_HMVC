# CodeIgniter-3.1.9_HMVC

# ตั้งค่าการเชื่อมต่อฐานข้อมูล

application/config/database.php
https://codeigniter.com/user_guide/database/configuration.html

# สร้างตารางเก็บ Session
https://codeigniter.com/user_guide/libraries/sessions.html?highlight=session#database-driver

CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `id` varchar(128) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
);