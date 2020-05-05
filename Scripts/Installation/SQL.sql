-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 05, 2020 at 07:51 PM
-- Server version: 5.7.29-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `fhedfbvc`
--

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `id` int(11) NOT NULL,
  `lid` int(11) NOT NULL,
  `zid` int(11) NOT NULL,
  `did` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT 'NA',
  `mac` varchar(255) NOT NULL DEFAULT 'NA',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `gpstime` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL
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
  `uid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL,
  `mqttu` varchar(255) NOT NULL DEFAULT '',
  `mqttp` varchar(255) NOT NULL DEFAULT '',
  `apub` varchar(255) NOT NULL DEFAULT '',
  `aprv` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `mac` varchar(255) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0'
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
  `uid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL,
  `zid` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `mqttu` varchar(255) NOT NULL DEFAULT '',
  `mqttp` varchar(255) NOT NULL DEFAULT '',
  `apub` varchar(255) NOT NULL DEFAULT '',
  `aprv` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `mac` varchar(255) NOT NULL DEFAULT '',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0'
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

--
-- Dumping data for table `mqttlz`
--

INSERT INTO `mqttlz` (`id`, `uid`, `lid`, `zn`, `time`) VALUES
(1, 0, 1, 'Office', 1587820104),
(2, 0, 1, 'Downstairs Bathroom', 1588379693),
(3, 0, 1, 'Boxroom', 1588380489),
(4, 0, 1, 'Lounge', 1588380508),
(5, 0, 1, 'Kitchen', 1588380520);

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
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
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
(18, 'Virtual Controller', 'Sensor', 1, 0, 'virtual-controller.png'),
(19, 'TestSensor', 'Sensor', 0, 0, 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `version` varchar(50) NOT NULL,
  `phpmyadmin` varchar(255) NOT NULL,
  `recaptcha` varchar(255) NOT NULL,
  `recaptchas` varchar(255) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `domainString` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `version`, `phpmyadmin`, `recaptcha`, `recaptchas`, `meta_title`, `meta_description`, `meta_keywords`, `domainString`) VALUES
(1, '0.1.0', 'phpmyadmin', '', '', 'HIAS', 'The Peter Moss COVID-19 Medical Support System Server is a locally hosted, secure NGINX server powering an online medical support system based on GeniSysAI. The server and support system provide an easy to use control panel to communicate with, monitor and control Peter Moss COVID-19 AI Research Project projects such as the COVID-19 Emergency Assistance Robot, the COVID-19 IoT Devices & AI projects.', '', 'TkdFUWpmZi9pNExjWnM5L0NDeEF2RkpialloaDZwZWp0UVJUSmpvdTVveTErZG50Y0VPVlYwNmduZnVKT3ovUTo619knGBt58Kuu/RtQ3Epy5g==');

-- --------------------------------------------------------

--
-- Table structure for table `tass`
--

CREATE TABLE `tass` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'OFFLINE',
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
  `sckport` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL DEFAULT 'default.png',
  `lt` varchar(255) NOT NULL DEFAULT '',
  `lg` varchar(255) NOT NULL DEFAULT '',
  `gpstime` int(11) NOT NULL DEFAULT '0',
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
-- Indexes for table `blocked`
--
ALTER TABLE `blocked`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banned` (`banned`);

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
  ADD KEY `pid` (`pid`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tass`
--
ALTER TABLE `tass`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `lid` (`lid`),
  ADD KEY `zid` (`zid`),
  ADD KEY `did` (`did`),
  ADD KEY `aid` (`aid`),
  ADD KEY `mid` (`mid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin` (`admin`),
  ADD KEY `gpstime` (`gpstime`);

--
-- AUTO_INCREMENT for dumped tables
--
