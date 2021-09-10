-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 10, 2021 at 02:05 AM
-- Server version: 8.0.26-0ubuntu0.20.04.2
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `amqpp`
--

CREATE TABLE `amqpp` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `permission` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `amqpp`
--

INSERT INTO `amqpp` (`id`, `uid`, `permission`) VALUES
(1, 8, 'administrator'),
(2, 8, 'managment'),
(3, 12, 'administrator'),
(4, 12, 'managment'),
(5, 16, 'administrator'),
(6, 16, 'managment'),
(7, 20, 'administrator'),
(8, 20, 'managment');

-- --------------------------------------------------------

--
-- Table structure for table `amqpu`
--

CREATE TABLE `amqpu` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `pw` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amqpvh`
--

CREATE TABLE `amqpvh` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `vhost` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amqpvhr`
--

CREATE TABLE `amqpvhr` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `vhost` varchar(255) NOT NULL,
  `rtype` varchar(255) NOT NULL,
  `rname` varchar(255) NOT NULL,
  `permission` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amqpvhrt`
--

CREATE TABLE `amqpvhrt` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `vhost` varchar(255) NOT NULL,
  `rtype` varchar(255) NOT NULL,
  `rname` varchar(255) NOT NULL,
  `permission` varchar(255) NOT NULL,
  `rkey` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `application_cats`
--

CREATE TABLE `application_cats` (
  `id` int NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `application_cats`
--

INSERT INTO `application_cats` (`id`, `category`) VALUES
(3, 'UI'),
(4, 'Android'),
(5, 'iOS'),
(6, 'AndroidWatch'),
(7, 'iOSWatch');

-- --------------------------------------------------------

--
-- Table structure for table `blocked`
--

CREATE TABLE `blocked` (
  `id` int NOT NULL,
  `ipv6` varchar(255) NOT NULL,
  `banned` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `building_types`
--

CREATE TABLE `building_types` (
  `id` int NOT NULL,
  `building` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `building_types`
--

INSERT INTO `building_types` (`id`, `building`) VALUES
(1, 'Hospital'),
(2, 'MedicalCenter'),
(3, 'Laboratory'),
(4, 'University'),
(5, 'College'),
(6, 'Association'),
(7, 'Foundation'),
(8, 'Office');

-- --------------------------------------------------------

--
-- Table structure for table `genisysai_chat`
--

CREATE TABLE `genisysai_chat` (
  `id` int NOT NULL,
  `uid` varchar(255) NOT NULL DEFAULT '',
  `isgenisys` int NOT NULL DEFAULT '0',
  `agent` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `timestamp` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hiasbch`
--

CREATE TABLE `hiasbch` (
  `id` int NOT NULL,
  `ic` varchar(255) NOT NULL DEFAULT '0',
  `dc` varchar(255) NOT NULL,
  `entity` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hiasbch`
--

INSERT INTO `hiasbch` (`id`, `ic`, `dc`, `entity`) VALUES
(1, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `hiasbch_contracts`
--

CREATE TABLE `hiasbch_contracts` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `contract` varchar(255) NOT NULL,
  `acc` varchar(255) NOT NULL,
  `abi` json NOT NULL,
  `bin` longtext NOT NULL,
  `txn` text NOT NULL,
  `uid` int NOT NULL,
  `time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hiasbch_transactions`
--

CREATE TABLE `hiasbch_transactions` (
  `id` int NOT NULL,
  `uid` varchar(255) NOT NULL DEFAULT '',
  `stfid` varchar(255) NOT NULL,
  `did` varchar(255) NOT NULL DEFAULT '',
  `aid` varchar(255) NOT NULL DEFAULT '',
  `agid` varchar(255) NOT NULL,
  `cid` varchar(255) NOT NULL DEFAULT '',
  `pid` varchar(255) NOT NULL DEFAULT '',
  `bid` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL,
  `hash` text NOT NULL,
  `time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hiascdi`
--

CREATE TABLE `hiascdi` (
  `id` int NOT NULL,
  `entity` varchar(255) NOT NULL,
  `hdsiv` varchar(50) NOT NULL,
  `ngsiv` varchar(50) NOT NULL,
  `local_ip` varchar(255) NOT NULL,
  `url` varchar(50) NOT NULL,
  `agents_url` varchar(255) NOT NULL,
  `entities_url` varchar(255) NOT NULL,
  `types_url` varchar(255) NOT NULL,
  `subscriptions_url` varchar(255) NOT NULL,
  `commands_url` varchar(255) NOT NULL,
  `registrations_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hiascdi`
--

INSERT INTO `hiascdi` (`id`, `entity`, `hdsiv`, `ngsiv`, `local_ip`, `url`, `agents_url`, `entities_url`, `types_url`, `subscriptions_url`, `commands_url`, `registrations_url`) VALUES
(1, '', 'v1', 'v2', '', 'hiascdi/v1', 'agents', 'entities', 'types', 'subscriptions', 'commands', 'registrations');

-- --------------------------------------------------------

--
-- Table structure for table `hiascdi_ai_models`
--

CREATE TABLE `hiascdi_ai_models` (
  `id` int NOT NULL,
  `model` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hiascdi_ai_models`
--

INSERT INTO `hiascdi_ai_models` (`id`, `model`) VALUES
(1, 'Facial Recognition'),
(2, 'Diagnosis'),
(3, 'Object Detection'),
(6, 'Natural Language Understanding');

-- --------------------------------------------------------

--
-- Table structure for table `hiascdi_ai_model_categories`
--

CREATE TABLE `hiascdi_ai_model_categories` (
  `id` int NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hiascdi_ai_model_categories`
--

INSERT INTO `hiascdi_ai_model_categories` (`id`, `category`) VALUES
(1, 'Classification'),
(2, 'Segmentation');

-- --------------------------------------------------------

--
-- Table structure for table `hiascdi_device_cats`
--

CREATE TABLE `hiascdi_device_cats` (
  `id` int NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hiascdi_device_cats`
--

INSERT INTO `hiascdi_device_cats` (`id`, `category`) VALUES
(1, 'environment'),
(2, 'diagnostics'),
(3, 'security'),
(4, 'radiology'),
(5, 'storage'),
(6, 'media'),
(7, 'zone'),
(8, 'bci'),
(9, 'lighting');

-- --------------------------------------------------------

--
-- Table structure for table `hiascdi_device_models`
--

CREATE TABLE `hiascdi_device_models` (
  `id` int NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hiascdi_device_models`
--

INSERT INTO `hiascdi_device_models` (`id`, `category`) VALUES
(10, 'magicLeap'),
(11, 'oculusRift'),
(12, 'up2'),
(13, 'rpi4'),
(14, 'rpi3b'),
(15, 'rpi3'),
(16, 'esp8266'),
(17, 'esp32'),
(18, 'generic');

-- --------------------------------------------------------

--
-- Table structure for table `hiashdi`
--

CREATE TABLE `hiashdi` (
  `id` int NOT NULL,
  `entity` varchar(255) NOT NULL,
  `hiashdiv` varchar(50) NOT NULL,
  `local_ip` varchar(255) NOT NULL,
  `url` varchar(50) NOT NULL,
  `locations_url` varchar(255) NOT NULL,
  `zones_url` varchar(255) NOT NULL,
  `data_url` varchar(255) NOT NULL,
  `statuses_url` varchar(255) NOT NULL,
  `life_url` varchar(255) NOT NULL,
  `sensors_url` varchar(255) NOT NULL,
  `actuators_url` varchar(255) NOT NULL,
  `commands_url` varchar(255) NOT NULL,
  `subscriptions_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hiashdi`
--

INSERT INTO `hiashdi` (`id`, `entity`, `hiashdiv`, `local_ip`, `url`, `locations_url`, `zones_url`, `data_url`, `statuses_url`, `life_url`, `sensors_url`, `actuators_url`, `commands_url`, `subscriptions_url`) VALUES
(1, '', 'v1', '', 'hiashdi/v1', 'location', 'zones', 'data', 'statuses', 'life', 'sensors', 'actuators', 'Commands', 'Subscriptions');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int NOT NULL,
  `uid` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL DEFAULT '0',
  `tuid` varchar(255) NOT NULL DEFAULT '',
  `tlid` varchar(255) NOT NULL DEFAULT '',
  `tzid` varchar(255) NOT NULL DEFAULT '',
  `tdid` varchar(255) NOT NULL DEFAULT '',
  `tsid` varchar(255) NOT NULL DEFAULT '',
  `taid` varchar(255) NOT NULL DEFAULT '',
  `tagid` varchar(255) NOT NULL DEFAULT '',
  `tcid` varchar(255) NOT NULL DEFAULT '',
  `tpid` varchar(255) NOT NULL DEFAULT '',
  `tbid` varchar(255) NOT NULL DEFAULT '',
  `trid` varchar(255) NOT NULL,
  `time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `id` int NOT NULL,
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
  `id` int NOT NULL,
  `ipv6` varchar(255) NOT NULL,
  `browser` text NOT NULL,
  `language` text NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttu`
--

CREATE TABLE `mqttu` (
  `id` int NOT NULL,
  `uid` int NOT NULL DEFAULT '0',
  `lid` int NOT NULL DEFAULT '0',
  `zid` int NOT NULL DEFAULT '0',
  `did` int NOT NULL DEFAULT '0',
  `aid` int NOT NULL DEFAULT '0',
  `pid` int NOT NULL DEFAULT '0',
  `bid` int NOT NULL DEFAULT '0',
  `uname` varchar(255) NOT NULL,
  `pw` varchar(255) NOT NULL,
  `super` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttua`
--

CREATE TABLE `mqttua` (
  `id` int NOT NULL,
  `uid` int NOT NULL DEFAULT '0',
  `lid` int NOT NULL DEFAULT '0',
  `zid` int NOT NULL DEFAULT '0',
  `did` int NOT NULL DEFAULT '0',
  `aid` int NOT NULL DEFAULT '0',
  `pid` int NOT NULL DEFAULT '0',
  `bid` int NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `rw` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `protocols`
--

CREATE TABLE `protocols` (
  `id` int NOT NULL,
  `protocol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `protocols`
--

INSERT INTO `protocols` (`id`, `protocol`) VALUES
(1, 'mqtt'),
(2, 'amqp'),
(3, 'coap'),
(4, 'http'),
(5, 'ul20'),
(6, 'lwm2m'),
(7, 'websocket'),
(8, 'onem2m'),
(9, 'sigfox'),
(10, 'lora'),
(11, 'nb-iot'),
(12, 'ec-gsm-iot'),
(13, 'lte-m'),
(14, 'cat-m'),
(15, '3g'),
(16, 'grps'),
(17, 'bluetooth'),
(18, 'ble');

-- --------------------------------------------------------

--
-- Table structure for table `robotics_categories`
--

CREATE TABLE `robotics_categories` (
  `id` int NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `robotics_categories`
--

INSERT INTO `robotics_categories` (`id`, `category`) VALUES
(1, 'Programmed'),
(2, 'Autonomous'),
(3, 'Teleoperated'),
(4, 'Humanoid'),
(5, 'Virtual Reality'),
(6, 'Mixed Reality'),
(7, 'Surveillance'),
(8, 'Maintenance'),
(9, 'Repairs');

-- --------------------------------------------------------

--
-- Table structure for table `robotics_types`
--

CREATE TABLE `robotics_types` (
  `id` int NOT NULL,
  `r_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `robotics_types`
--

INSERT INTO `robotics_types` (`id`, `r_type`) VALUES
(1, 'EMAR'),
(2, 'EMAR-Mini');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `lid` varchar(255) NOT NULL,
  `aid` varchar(255) NOT NULL,
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
  `ip` varchar(255) NOT NULL,
  `installed` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `lid`, `aid`, `version`, `phpmyadmin`, `recaptcha`, `recaptchas`, `gmaps`, `lt`, `lg`, `meta_title`, `meta_description`, `meta_keywords`, `domainString`, `ip`, `installed`) VALUES
(1, '', '', '3.0.0', 'phpmyadmin', '', '', '', '', '', 'Hospital Intelligent Automation Server', 'Open-source Hospital Intelligent Automation System & Hospital Information/Management System. A locally hosted web/IoT server and proxy for managing a network of open-source, modular, intelligent devices, robotics and applications.', '', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `technologies`
--

CREATE TABLE `technologies` (
  `id` int NOT NULL,
  `technology` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `technologies`
--

INSERT INTO `technologies` (`id`, `technology`) VALUES
(1, 'Artificial Intelligence'),
(2, 'Internet of Things'),
(3, 'Blockchain'),
(4, 'Brain Computer Interface'),
(5, 'Virtual Reality'),
(6, 'Mixed Reality'),
(7, 'Augmented Reality'),
(8, 'Spatial Computing');

-- --------------------------------------------------------

--
-- Table structure for table `things`
--

CREATE TABLE `things` (
  `id` int NOT NULL,
  `pub` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_cats`
--

CREATE TABLE `user_cats` (
  `id` int NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_cats`
--

INSERT INTO `user_cats` (`id`, `category`) VALUES
(1, 'Management'),
(2, 'Directors'),
(3, 'Administration'),
(4, 'Supervisor'),
(5, 'Doctor'),
(6, 'Nurse'),
(7, 'Security'),
(8, 'Network Security'),
(9, 'Developer');

-- --------------------------------------------------------

--
-- Table structure for table `zone_cats`
--

CREATE TABLE `zone_cats` (
  `id` int NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zone_cats`
--

INSERT INTO `zone_cats` (`id`, `category`) VALUES
(1, 'A&E'),
(2, 'Casualty'),
(3, 'Day room'),
(4, 'Delivery Room'),
(5, 'Dispensary'),
(6, 'Emergency Department'),
(7, 'Emergency Room'),
(8, 'High Dependency Unit'),
(9, 'Intensive Care Unit (ICU)'),
(10, 'Maternity Ward'),
(11, 'Nursery'),
(12, 'Operating Room'),
(13, 'Pharmacy'),
(14, 'Sick Room'),
(15, 'Surgery'),
(16, 'Office'),
(17, 'Reception'),
(18, 'Bathroom'),
(19, 'Kitchen'),
(20, 'Yard'),
(21, 'Gardens'),
(22, 'Terrace'),
(23, 'Dormitory'),
(24, 'Room'),
(25, 'Storage');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amqpp`
--
ALTER TABLE `amqpp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `amqpu`
--
ALTER TABLE `amqpu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `amqpvh`
--
ALTER TABLE `amqpvh`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `amqpvhr`
--
ALTER TABLE `amqpvhr`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `amqpvhrt`
--
ALTER TABLE `amqpvhrt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `application_cats`
--
ALTER TABLE `application_cats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blocked`
--
ALTER TABLE `blocked`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banned` (`banned`);

--
-- Indexes for table `building_types`
--
ALTER TABLE `building_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genisysai_chat`
--
ALTER TABLE `genisysai_chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `isgenisys` (`isgenisys`),
  ADD KEY `agent` (`agent`);

--
-- Indexes for table `hiasbch`
--
ALTER TABLE `hiasbch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dc` (`dc`);

--
-- Indexes for table `hiasbch_contracts`
--
ALTER TABLE `hiasbch_contracts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hiasbch_transactions`
--
ALTER TABLE `hiasbch_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `did` (`did`),
  ADD KEY `aid` (`aid`),
  ADD KEY `agid` (`agid`);

--
-- Indexes for table `hiascdi`
--
ALTER TABLE `hiascdi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hiascdi_ai_models`
--
ALTER TABLE `hiascdi_ai_models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hiascdi_ai_model_categories`
--
ALTER TABLE `hiascdi_ai_model_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hiascdi_device_cats`
--
ALTER TABLE `hiascdi_device_cats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hiascdi_device_models`
--
ALTER TABLE `hiascdi_device_models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hiashdi`
--
ALTER TABLE `hiashdi`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `taid` (`taid`),
  ADD KEY `tagid` (`tagid`);

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
-- Indexes for table `protocols`
--
ALTER TABLE `protocols`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `robotics_categories`
--
ALTER TABLE `robotics_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `robotics_types`
--
ALTER TABLE `robotics_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `did` (`aid`);

--
-- Indexes for table `technologies`
--
ALTER TABLE `technologies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `things`
--
ALTER TABLE `things`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zone_cats`
--
ALTER TABLE `zone_cats`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amqpp`
--
ALTER TABLE `amqpp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amqpu`
--
ALTER TABLE `amqpu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amqpvh`
--
ALTER TABLE `amqpvh`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amqpvhr`
--
ALTER TABLE `amqpvhr`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amqpvhrt`
--
ALTER TABLE `amqpvhrt`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `application_cats`
--
ALTER TABLE `application_cats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blocked`
--
ALTER TABLE `blocked`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `building_types`
--
ALTER TABLE `building_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genisysai_chat`
--
ALTER TABLE `genisysai_chat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hiasbch`
--
ALTER TABLE `hiasbch`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hiasbch_contracts`
--
ALTER TABLE `hiasbch_contracts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hiasbch_transactions`
--
ALTER TABLE `hiasbch_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hiascdi`
--
ALTER TABLE `hiascdi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hiascdi_ai_models`
--
ALTER TABLE `hiascdi_ai_models`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hiascdi_ai_model_categories`
--
ALTER TABLE `hiascdi_ai_model_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hiascdi_device_cats`
--
ALTER TABLE `hiascdi_device_cats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hiascdi_device_models`
--
ALTER TABLE `hiascdi_device_models`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hiashdi`
--
ALTER TABLE `hiashdi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loginsf`
--
ALTER TABLE `loginsf`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `mqttu`
--
ALTER TABLE `mqttu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `mqttua`
--
ALTER TABLE `mqttua`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `protocols`
--
ALTER TABLE `protocols`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `robotics_categories`
--
ALTER TABLE `robotics_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `robotics_types`
--
ALTER TABLE `robotics_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `technologies`
--
ALTER TABLE `technologies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `things`
--
ALTER TABLE `things`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zone_cats`
--
ALTER TABLE `zone_cats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
