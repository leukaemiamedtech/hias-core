-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 24, 2020 at 03:57 AM
-- Server version: 5.7.31-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `amqpp`
--

CREATE TABLE `amqpp` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `permission` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amqpu`
--

CREATE TABLE `amqpu` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `pw` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amqpvh`
--

CREATE TABLE `amqpvh` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `vhost` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `amqpvhr`
--

CREATE TABLE `amqpvhr` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
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
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `vhost` varchar(255) NOT NULL,
  `rtype` varchar(255) NOT NULL,
  `rname` varchar(255) NOT NULL,
  `permission` varchar(255) NOT NULL,
  `rkey` varchar(255) NOT NULL
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
-- Table structure for table `cbAI`
--

CREATE TABLE `cbAI` (
  `id` int(11) NOT NULL,
  `model` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cbAI`
--

INSERT INTO `cbAI` (`id`, `model`) VALUES
(1, 'TassAI Facial Recognition (CNN)'),
(2, 'TassAI Facial Classification (CNN)'),
(3, 'Object Detection (CNN)'),
(4, 'Acute Lymphoblastic Leukemia Classification (CNN)'),
(5, 'COVID-19 Classification (CNN)'),
(6, 'GeniSysAI Natural Language Understanding'),
(7, 'Acute Myeloid Leukemia Classification (CNN)'),
(8, 'COVID-19 Classification (xDNN)');

-- --------------------------------------------------------

--
-- Table structure for table `cbApplicationCats`
--

CREATE TABLE `cbApplicationCats` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cbApplicationCats`
--

INSERT INTO `cbApplicationCats` (`id`, `category`) VALUES
(1, 'System'),
(2, 'IoT Agent');

-- --------------------------------------------------------

--
-- Table structure for table `cbDeviceCats`
--

CREATE TABLE `cbDeviceCats` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cbDeviceCats`
--

INSERT INTO `cbDeviceCats` (`id`, `category`) VALUES
(1, 'Camera'),
(4, 'Scanner'),
(5, 'MixedReality'),
(6, 'VirtualReality'),
(7, 'Server'),
(8, 'TassAI'),
(9, 'GeniSysAI'),
(10, 'EMAR'),
(11, 'AMLClassifier'),
(12, 'ALLClassifier'),
(13, 'COVIDClassifier'),
(14, 'SkinCancerClassifier');

-- --------------------------------------------------------

--
-- Table structure for table `cbPatientsCats`
--

CREATE TABLE `cbPatientsCats` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cbPatientsCats`
--

INSERT INTO `cbPatientsCats` (`id`, `category`) VALUES
(1, 'Primary Care'),
(2, 'Specialty Care'),
(3, 'Emergency Care'),
(4, 'Urgent Care'),
(5, 'Long-term Care'),
(6, 'Hospice Care');

-- --------------------------------------------------------

--
-- Table structure for table `cbProtocols`
--

CREATE TABLE `cbProtocols` (
  `id` int(11) NOT NULL,
  `protocol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cbProtocols`
--

INSERT INTO `cbProtocols` (`id`, `protocol`) VALUES
(1, 'MQTT'),
(2, 'AMQP'),
(3, 'CoAP'),
(4, 'HTTP'),
(5, 'Websockets'),
(6, 'LwM2M');

-- --------------------------------------------------------

--
-- Table structure for table `cbUserCats`
--

CREATE TABLE `cbUserCats` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cbUserCats`
--

INSERT INTO `cbUserCats` (`id`, `category`) VALUES
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
-- Table structure for table `cbZoneCats`
--

CREATE TABLE `cbZoneCats` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cbZoneCats`
--

INSERT INTO `cbZoneCats` (`id`, `category`) VALUES
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
(24, 'Room');

-- --------------------------------------------------------

--
-- Table structure for table `contextbroker`
--

CREATE TABLE `contextbroker` (
  `id` int(11) NOT NULL,
  `hdsiv` varchar(50) NOT NULL,
  `url` varchar(50) NOT NULL,
  `local_ip` varchar(255) NOT NULL,
  `about_url` varchar(255) NOT NULL,
  `entities_url` varchar(255) NOT NULL,
  `types_url` varchar(255) NOT NULL,
  `subscriptions_url` varchar(255) NOT NULL,
  `registrations_url` varchar(255) NOT NULL,
  `agents_url` varchar(255) NOT NULL,
  `commands_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contextbroker`
--

INSERT INTO `contextbroker` (`id`, `hdsiv`, `url`, `local_ip`, `about_url`, `entities_url`, `types_url`, `subscriptions_url`, `registrations_url`, `agents_url`, `commands_url`) VALUES
(1, '1', 'ContextBroker', '', 'v1/about', 'v1/entities', 'v1/types', 'v1/subscriptions', 'v1/registrations', 'v1/agents', 'v1/commands');

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
-- Table structure for table `genisysai`
--

CREATE TABLE `genisysai` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `isGeniSys` tinyint(4) NOT NULL DEFAULT '0',
  `device` varchar(255) NOT NULL,
  `chat` text NOT NULL,
  `timestamp` int(11) NOT NULL
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
-- Table structure for table `models`
--

CREATE TABLE `models` (
  `id` int(11) NOT NULL,
  `pub` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqtta`
--

CREATE TABLE `mqtta` (
  `id` int(11) NOT NULL,
  `apub` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttl`
--

CREATE TABLE `mqttl` (
  `id` int(11) NOT NULL,
  `pub` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttld`
--

CREATE TABLE `mqttld` (
  `id` int(11) NOT NULL,
  `apub` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttlz`
--

CREATE TABLE `mqttlz` (
  `id` int(11) NOT NULL,
  `pub` varchar(255) NOT NULL DEFAULT ''
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
  `pub` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `bcaddress` varchar(255) NOT NULL DEFAULT '',
  `bcpass` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE `sensors` (
  `id` int(11) NOT NULL,
  `pub` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `hasAction` tinyint(4) NOT NULL DEFAULT '0',
  `hasCommand` tinyint(4) NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(1, 0, '2.0.0', 'phpmyadmin', '', '', '', '', '', 'HIAS Hospital Intelligent Automation System', 'Open-source Hospital Intelligent Automation System & Hospital Information/Management System. A locally hosted web/IoT server and proxy for managing a network of open-source, modular, intelligent devices, robotics and applications.', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tassai`
--

CREATE TABLE `tassai` (
  `id` int(11) NOT NULL,
  `pub` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `things`
--

CREATE TABLE `things` (
  `id` int(11) NOT NULL,
  `pub` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `tuid` int(11) NOT NULL DEFAULT '0',
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
  `pub` varchar(255) NOT NULL DEFAULT '',
  `aid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL,
  `bcaddress` varchar(255) NOT NULL,
  `bcpw` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Indexes for table `cbAI`
--
ALTER TABLE `cbAI`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbApplicationCats`
--
ALTER TABLE `cbApplicationCats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbDeviceCats`
--
ALTER TABLE `cbDeviceCats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbPatientsCats`
--
ALTER TABLE `cbPatientsCats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbProtocols`
--
ALTER TABLE `cbProtocols`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbUserCats`
--
ALTER TABLE `cbUserCats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbZoneCats`
--
ALTER TABLE `cbZoneCats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contextbroker`
--
ALTER TABLE `contextbroker`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `genisysai`
--
ALTER TABLE `genisysai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `isGeniSys` (`isGeniSys`),
  ADD KEY `uid` (`uid`);

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
-- Indexes for table `models`
--
ALTER TABLE `models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mqtta`
--
ALTER TABLE `mqtta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mqttl`
--
ALTER TABLE `mqttl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mqttld`
--
ALTER TABLE `mqttld`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mqttlz`
--
ALTER TABLE `mqttlz`
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
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `tassai`
--
ALTER TABLE `tassai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `things`
--
ALTER TABLE `things`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amqpp`
--
ALTER TABLE `amqpp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `amqpu`
--
ALTER TABLE `amqpu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `amqpvh`
--
ALTER TABLE `amqpvh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `amqpvhr`
--
ALTER TABLE `amqpvhr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `amqpvhrt`
--
ALTER TABLE `amqpvhrt`
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
-- AUTO_INCREMENT for table `cbAI`
--
ALTER TABLE `cbAI`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cbApplicationCats`
--
ALTER TABLE `cbApplicationCats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cbDeviceCats`
--
ALTER TABLE `cbDeviceCats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cbPatientsCats`
--
ALTER TABLE `cbPatientsCats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cbProtocols`
--
ALTER TABLE `cbProtocols`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cbUserCats`
--
ALTER TABLE `cbUserCats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cbZoneCats`
--
ALTER TABLE `cbZoneCats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contextbroker`
--
ALTER TABLE `contextbroker`
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
-- AUTO_INCREMENT for table `genisysai`
--
ALTER TABLE `genisysai`
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
-- AUTO_INCREMENT for table `models`
--
ALTER TABLE `models`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tassai`
--
ALTER TABLE `tassai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `things`
--
ALTER TABLE `things`
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