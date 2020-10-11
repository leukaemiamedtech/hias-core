-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 10, 2020 at 12:02 PM
-- Server version: 5.7.31-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hiasssl`
--

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL DEFAULT '0',
  `zid` int(11) NOT NULL DEFAULT '0',
  `did` int(11) NOT NULL DEFAULT '0',
  `bcaddress` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT 'NA',
  `mac` varchar(255) NOT NULL DEFAULT 'NA',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `gpstime` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blockchain`
--

CREATE TABLE `blockchain` (
  `id` int(11) NOT NULL,
  `dc` int(11) NOT NULL DEFAULT '0',
  `pc` int(11) NOT NULL DEFAULT '0',
  `ic` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blocked`
--

CREATE TABLE `blocked` (
  `id` int(11) NOT NULL,
  `ipv6` varchar(255) NOT NULL,
  `banned` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contract` varchar(255) NOT NULL,
  `acc` varchar(255) NOT NULL,
  `abi` json NOT NULL,
  `txn` text NOT NULL,
  `uid` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `covid19data`
--

CREATE TABLE `covid19data` (
  `id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `lat` varchar(255) NOT NULL,
  `lng` varchar(255) NOT NULL,
  `confirmed` int(11) NOT NULL DEFAULT '0',
  `deaths` int(11) NOT NULL DEFAULT '0',
  `recovered` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `file` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `timeadded` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `covid19pulls`
--

CREATE TABLE `covid19pulls` (
  `id` int(11) NOT NULL,
  `pulldate` date NOT NULL,
  `datefrom` date NOT NULL,
  `dateto` date NOT NULL,
  `rows` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emar`
--

CREATE TABLE `emar` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL DEFAULT '0',
  `zid` int(11) NOT NULL DEFAULT '0',
  `did` int(11) NOT NULL DEFAULT '0',
  `aid` int(11) NOT NULL DEFAULT '0',
  `mid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL,
  `mac` varchar(255) NOT NULL,
  `sport` varchar(255) NOT NULL,
  `sdir` varchar(255) NOT NULL,
  `sportf` varchar(255) NOT NULL,
  `sckport` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `genisysai`
--

CREATE TABLE `genisysai` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL DEFAULT '0',
  `zid` int(11) NOT NULL DEFAULT '0',
  `did` int(11) NOT NULL DEFAULT '0',
  `aid` int(11) NOT NULL DEFAULT '0',
  `mid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `mac` varchar(255) NOT NULL DEFAULT '',
  `sport` varchar(255) NOT NULL DEFAULT '',
  `sportf` varchar(255) NOT NULL DEFAULT '',
  `sckport` varchar(255) NOT NULL DEFAULT '',
  `strdir` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `genisysainlu`
--

CREATE TABLE `genisysainlu` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL DEFAULT '0',
  `zid` int(11) NOT NULL DEFAULT '0',
  `did` int(11) NOT NULL DEFAULT '0',
  `apidir` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL,
  `hash` int(11) NOT NULL DEFAULT '0',
  `tuid` int(11) NOT NULL DEFAULT '0',
  `tlid` int(11) NOT NULL DEFAULT '0',
  `tzid` int(11) NOT NULL DEFAULT '0',
  `tdid` int(11) NOT NULL DEFAULT '0',
  `tsid` int(11) NOT NULL DEFAULT '0',
  `taid` int(11) NOT NULL DEFAULT '0',
  `tcid` int(11) NOT NULL DEFAULT '0',
  `tpid` int(11) NOT NULL DEFAULT '0',
  `tbid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `ipv6` varchar(255) NOT NULL,
  `browser` text NOT NULL,
  `language` text NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loginsf`
--

CREATE TABLE `loginsf` (
  `id` int(11) NOT NULL,
  `ipv6` varchar(255) NOT NULL,
  `browser` text NOT NULL,
  `language` text NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqtta`
--

CREATE TABLE `mqtta` (
  `id` int(11) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `iotJumpWay` int(11) NOT NULL DEFAULT '0',
  `cancelled` int(11) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'OFFLINE',
  `uid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL,
  `mqttu` varchar(255) NOT NULL DEFAULT '',
  `mqttp` varchar(255) NOT NULL DEFAULT '',
  `apub` varchar(255) NOT NULL DEFAULT '',
  `aprv` varchar(255) NOT NULL DEFAULT '',
  `bcaddress` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `mac` varchar(255) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `cpu` decimal(10,2) NOT NULL DEFAULT '0.00',
  `mem` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hdd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tempr` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttl`
--

CREATE TABLE `mqttl` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `mac` varchar(255) NOT NULL DEFAULT '',
  `zones` int(11) NOT NULL DEFAULT '0',
  `devices` int(11) NOT NULL DEFAULT '0',
  `apps` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttld`
--

CREATE TABLE `mqttld` (
  `id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'OFFLINE',
  `uid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL,
  `zid` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `mqttu` varchar(255) NOT NULL DEFAULT '',
  `mqttp` varchar(255) NOT NULL DEFAULT '',
  `bcaddress` varchar(255) NOT NULL DEFAULT '',
  `bcpw` varchar(255) NOT NULL DEFAULT '',
  `apub` varchar(255) NOT NULL DEFAULT '',
  `aprv` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `mac` varchar(255) NOT NULL DEFAULT '',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `cpu` decimal(10,2) NOT NULL DEFAULT '0.00',
  `mem` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hdd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tempr` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttlz`
--

CREATE TABLE `mqttlz` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL DEFAULT '0',
  `zn` varchar(255) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttu`
--

CREATE TABLE `mqttu` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL DEFAULT '0',
  `zid` int(11) NOT NULL DEFAULT '0',
  `did` int(11) NOT NULL DEFAULT '0',
  `aid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `uname` varchar(255) NOT NULL,
  `pw` varchar(255) NOT NULL,
  `super` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttua`
--

CREATE TABLE `mqttua` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL DEFAULT '0',
  `zid` int(11) NOT NULL DEFAULT '0',
  `did` int(11) NOT NULL DEFAULT '0',
  `aid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `rw` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL DEFAULT '0',
  `aid` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `admitted` tinyint(1) NOT NULL DEFAULT '0',
  `discharged` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `bcaddress` varchar(255) NOT NULL,
  `bcpass` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL DEFAULT 'default.png',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `gpstime` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE `sensors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `hasAction` tinyint(4) NOT NULL DEFAULT '0',
  `hasCommand` tinyint(4) NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sensors`
--

INSERT INTO `sensors` (`id`, `name`, `type`, `hasAction`, `hasCommand`, `image`) VALUES
(1, 'LED', 'Actuator', 0, 1, 'led.jpg'),
(2, 'Button', 'Actuator', 1, 0, 'button.jpg'),
(3, 'Reed Switch', 'Sensor', 1, 0, 'reed-switch.jpg'),
(4, 'Motion Sensor', 'Sensor', 1, 0, 'motion-sensor.jpg'),
(5, 'Light Sensor', 'Sensor', 1, 0, 'light-sensor.jpg'),
(6, 'Sound Sensor', 'Sensor', 1, 0, 'sound-sensor.jpg'),
(7, 'Camera', 'Sensor', 1, 0, 'cctv.jpg'),
(8, 'Buzzer', 'Actuator', 0, 1, 'buzzer.jpg'),
(9, 'Moisture Sensor', 'Sensor', 1, 0, 'moisture-sensor.jpg'),
(10, 'Rain Sensor', 'Sensor', 1, 0, 'RainSensor.jpg'),
(11, 'Water Level Sensor', 'Sensor', 1, 0, 'WaterLevelSensor.jpg'),
(12, 'Temperature Sensor', 'Sensor', 1, 0, 'temperature.jpg'),
(13, 'Servo', 'Actuator', 0, 1, 'servo.jpg'),
(14, 'Servo Controller', 'Actuator', 1, 0, 'servoController.jpg'),
(15, 'Relay', 'Actuator', 0, 1, 'relay.jpg'),
(16, 'NFC Scanner', 'Sensor', 1, 0, 'NFCScanner.jpg'),
(17, 'LCD Keypad (4 Buttons)', 'Sensor', 1, 1, 'LCD-KeyPad-4.jpg'),
(18, 'Virtual Controller', 'Sensor', 1, 0, 'virtual-controller.png');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `version` varchar(50) NOT NULL,
  `phpmyadmin` varchar(255) NOT NULL,
  `recaptcha` varchar(255) NOT NULL,
  `recaptchas` varchar(255) NOT NULL,
  `gmaps` varchar(255) NOT NULL,
  `lt` varchar(255) NOT NULL,
  `lg` varchar(255) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `domainString` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `aid`, `version`, `phpmyadmin`, `recaptcha`, `recaptchas`, `gmaps`, `lt`, `lg`, `meta_title`, `meta_description`, `meta_keywords`, `domainString`, `ip`) VALUES
(1, 0, '1.0.2', 'phpmyadmin', '', '', '', '', '', 'HIAS Hospital Intelligent Automation System', 'Open-source Hospital Intelligent Automation System & Hospital Information/Management System. A locally hosted web/IoT server and proxy for managing a network of open-source, modular, intelligent devices, robotics and applications.', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `did` int(11) NOT NULL DEFAULT '0',
  `aid` int(11) NOT NULL DEFAULT '0',
  `cid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL,
  `hash` text NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `cancelled` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL DEFAULT '0',
  `aid` int(11) NOT NULL DEFAULT '0',
  `admin` int(11) NOT NULL DEFAULT '0',
  `patients` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `bcaddress` varchar(255) NOT NULL,
  `bcpw` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nfc` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL DEFAULT 'default.png',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `gpstime` int(11) NOT NULL DEFAULT '0',
  `cz` int(11) NOT NULL DEFAULT '0',
  `czt` int(11) NOT NULL DEFAULT '0',
  `welcomed` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gpstime` (`gpstime`),
  ADD KEY `lid` (`lid`),
  ADD KEY `zid` (`zid`),
  ADD KEY `did` (`did`);

--
-- Indexes for table `blockchain`
--
ALTER TABLE `blockchain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dc` (`dc`);

--
-- Indexes for table `blocked`
--
ALTER TABLE `blocked`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banned` (`banned`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `covid19data`
--
ALTER TABLE `covid19data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country` (`country`),
  ADD KEY `province` (`province`),
  ADD KEY `timeadded` (`timeadded`);

--
-- Indexes for table `covid19pulls`
--
ALTER TABLE `covid19pulls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emar`
--
ALTER TABLE `emar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `lid` (`lid`),
  ADD KEY `zid` (`zid`),
  ADD KEY `did` (`did`),
  ADD KEY `aid` (`aid`),
  ADD KEY `mid` (`mid`);

--
-- Indexes for table `genisysai`
--
ALTER TABLE `genisysai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `lid` (`lid`),
  ADD KEY `zid` (`zid`),
  ADD KEY `did` (`did`),
  ADD KEY `aid` (`aid`),
  ADD KEY `mid` (`mid`);

--
-- Indexes for table `genisysainlu`
--
ALTER TABLE `genisysainlu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lid` (`lid`),
  ADD KEY `zid` (`zid`),
  ADD KEY `did` (`did`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `tuid` (`tuid`),
  ADD KEY `tzne` (`tzid`),
  ADD KEY `tlid` (`tlid`),
  ADD KEY `tdid` (`tdid`),
  ADD KEY `sid` (`tsid`),
  ADD KEY `taid` (`taid`);

--
-- Indexes for table `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loginsf`
--
ALTER TABLE `loginsf`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mqtta`
--
ALTER TABLE `mqtta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time` (`time`),
  ADD KEY `lid` (`lid`),
  ADD KEY `mqttu` (`mqttu`),
  ADD KEY `pid` (`pid`),
  ADD KEY `admin` (`admin`),
  ADD KEY `cancelled` (`cancelled`);

--
-- Indexes for table `mqttl`
--
ALTER TABLE `mqttl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time` (`time`),
  ADD KEY `apps` (`apps`),
  ADD KEY `name` (`name`),
  ADD KEY `devices` (`devices`),
  ADD KEY `zones` (`zones`);

--
-- Indexes for table `mqttld`
--
ALTER TABLE `mqttld`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time` (`time`),
  ADD KEY `lid` (`lid`),
  ADD KEY `mqttu` (`mqttu`),
  ADD KEY `zid` (`zid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `bid` (`bid`);

--
-- Indexes for table `mqttlz`
--
ALTER TABLE `mqttlz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `lid` (`lid`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `mqttu`
--
ALTER TABLE `mqttu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lid` (`lid`),
  ADD KEY `znid` (`zid`),
  ADD KEY `did` (`did`),
  ADD KEY `aid` (`aid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `bid` (`bid`);

--
-- Indexes for table `mqttua`
--
ALTER TABLE `mqttua`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lid` (`lid`),
  ADD KEY `zid` (`zid`),
  ADD KEY `did` (`did`),
  ADD KEY `aid` (`aid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `lid_2` (`lid`),
  ADD KEY `zid_2` (`zid`),
  ADD KEY `did_2` (`did`),
  ADD KEY `aid_2` (`aid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `bid` (`bid`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gpstime` (`gpstime`);

--
-- Indexes for table `sensors`
--
ALTER TABLE `sensors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hasAction` (`hasAction`),
  ADD KEY `hasCommand` (`hasCommand`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `did` (`aid`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `did` (`did`),
  ADD KEY `aid` (`aid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin` (`admin`),
  ADD KEY `gpstime` (`gpstime`),
  ADD KEY `cz` (`cz`),
  ADD KEY `czt` (`czt`),
  ADD KEY `welcomed` (`welcomed`),
  ADD KEY `cancelled` (`cancelled`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `blockchain`
--
ALTER TABLE `blockchain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `blocked`
--
ALTER TABLE `blocked`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `covid19data`
--
ALTER TABLE `covid19data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `covid19pulls`
--
ALTER TABLE `covid19pulls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `emar`
--
ALTER TABLE `emar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `genisysai`
--
ALTER TABLE `genisysai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `genisysainlu`
--
ALTER TABLE `genisysainlu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `loginsf`
--
ALTER TABLE `loginsf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mqtta`
--
ALTER TABLE `mqtta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mqttl`
--
ALTER TABLE `mqttl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mqttld`
--
ALTER TABLE `mqttld`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mqttlz`
--
ALTER TABLE `mqttlz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mqttu`
--
ALTER TABLE `mqttu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mqttua`
--
ALTER TABLE `mqttua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sensors`
--
ALTER TABLE `sensors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;