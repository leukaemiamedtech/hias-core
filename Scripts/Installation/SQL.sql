-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 04, 2020 at 07:53 PM
-- Server version: 5.7.30-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.6

SET SQL_MODE
= "NO_AUTO_VALUE_ON_ZERO";
SET time_zone
= "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fhedfbvc`
--

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds`
(
  `id` int
(11) NOT NULL,
  `lid` int
(11) NOT NULL,
  `zid` int
(11) NOT NULL,
  `did` int
(11) NOT NULL,
  `ip` varchar
(255) NOT NULL DEFAULT 'NA',
  `mac` varchar
(255) NOT NULL DEFAULT 'NA',
  `lt` varchar
(255) NOT NULL DEFAULT '',
  `lg` varchar
(255) NOT NULL DEFAULT '',
  `gpstime` int
(11) NOT NULL DEFAULT '0',
  `created` int
(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blocked`
--

CREATE TABLE `blocked`
(
  `id` int
(11) NOT NULL,
  `ipv6` varchar
(255) NOT NULL,
  `banned` int
(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `covid19data`
--

CREATE TABLE `covid19data`
(
  `id` int
(11) NOT NULL,
  `country` varchar
(255) NOT NULL,
  `province` varchar
(255) NOT NULL,
  `lat` varchar
(255) NOT NULL,
  `lng` varchar
(255) NOT NULL,
  `confirmed` int
(11) NOT NULL DEFAULT '0',
  `deaths` int
(11) NOT NULL DEFAULT '0',
  `recovered` int
(11) NOT NULL DEFAULT '0',
  `active` int
(11) NOT NULL DEFAULT '0',
  `file` varchar
(255) NOT NULL,
  `date` datetime NOT NULL,
  `timeadded` int
(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `covid19pulls`
--

CREATE TABLE `covid19pulls`
(
  `id` int
(11) NOT NULL,
  `pulldate` date NOT NULL,
  `datefrom` date NOT NULL,
  `dateto` date NOT NULL,
  `rows` int
(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emar`
--

CREATE TABLE `emar`
(
  `id` int
(11) NOT NULL,
  `name` varchar
(255) NOT NULL,
  `name2` varchar
(255) NOT NULL,
  `name3` varchar
(255) NOT NULL,
  `uid` int
(11) NOT NULL DEFAULT '0',
  `lid` int
(11) NOT NULL DEFAULT '0',
  `zid` int
(11) NOT NULL DEFAULT '0',
  `did` int
(11) NOT NULL DEFAULT '0',
  `did2` int
(11) NOT NULL,
  `did3` int
(11) NOT NULL,
  `aid` int
(11) NOT NULL DEFAULT '0',
  `mid` int
(11) NOT NULL DEFAULT '0',
  `ip` varchar
(255) NOT NULL DEFAULT '',
  `ip2` varchar
(255) NOT NULL,
  `ip3` varchar
(255) NOT NULL,
  `mac` varchar
(255) NOT NULL DEFAULT '',
  `mac2` varchar
(255) NOT NULL,
  `mac3` varchar
(255) NOT NULL,
  `sport` varchar
(255) NOT NULL DEFAULT '',
  `sport2` varchar
(255) NOT NULL,
  `sport3` varchar
(255) NOT NULL,
  `sdir` varchar
(255) NOT NULL,
  `sdir2` varchar
(255) NOT NULL,
  `sdir3` varchar
(255) NOT NULL,
  `sportf` varchar
(255) NOT NULL DEFAULT '',
  `sportf2` varchar
(255) NOT NULL,
  `sportf3` varchar
(255) NOT NULL,
  `sckport` varchar
(255) NOT NULL DEFAULT '',
  `sckport2` varchar
(255) NOT NULL,
  `sckport3` varchar
(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins`
(
  `id` int
(11) NOT NULL,
  `ipv6` varchar
(255) NOT NULL,
  `browser` text NOT NULL,
  `language` text NOT NULL,
  `time` varchar
(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loginsf`
--

CREATE TABLE `loginsf`
(
  `id` int
(11) NOT NULL,
  `ipv6` varchar
(255) NOT NULL,
  `browser` text NOT NULL,
  `language` text NOT NULL,
  `time` varchar
(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqtta`
--

CREATE TABLE `mqtta`
(
  `id` int
(11) NOT NULL,
  `status` varchar
(255) NOT NULL DEFAULT 'OFFLINE',
  `uid` int
(11) NOT NULL DEFAULT '0',
  `pid` int
(11) NOT NULL DEFAULT '0',
  `lid` int
(11) NOT NULL,
  `mqttu` varchar
(255) NOT NULL DEFAULT '',
  `mqttp` varchar
(255) NOT NULL DEFAULT '',
  `apub` varchar
(255) NOT NULL DEFAULT '',
  `aprv` varchar
(255) NOT NULL DEFAULT '',
  `name` varchar
(255) NOT NULL DEFAULT '',
  `lt` varchar
(255) NOT NULL DEFAULT '',
  `lg` varchar
(255) NOT NULL DEFAULT '',
  `ip` varchar
(255) NOT NULL DEFAULT '',
  `mac` varchar
(255) NOT NULL DEFAULT '',
  `time` int
(11) NOT NULL DEFAULT '0',
  `cpu` decimal
(10,2) NOT NULL DEFAULT '0.00',
  `mem` decimal
(10,2) NOT NULL DEFAULT '0.00',
  `hdd` decimal
(10,2) NOT NULL DEFAULT '0.00',
  `tempr` decimal
(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttl`
--

CREATE TABLE `mqttl`
(
  `id` int
(11) NOT NULL,
  `name` varchar
(255) NOT NULL DEFAULT '',
  `ip` varchar
(255) NOT NULL DEFAULT '',
  `mac` varchar
(255) NOT NULL DEFAULT '',
  `zones` int
(11) NOT NULL DEFAULT '0',
  `devices` int
(11) NOT NULL DEFAULT '0',
  `apps` int
(11) NOT NULL DEFAULT '0',
  `time` int
(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttld`
--

CREATE TABLE `mqttld`
(
  `id` int
(11) NOT NULL,
  `status` varchar
(255) NOT NULL DEFAULT 'OFFLINE',
  `uid` int
(11) NOT NULL DEFAULT '0',
  `lid` int
(11) NOT NULL,
  `zid` int
(11) NOT NULL DEFAULT '0',
  `bid` int
(11) NOT NULL DEFAULT '0',
  `mqttu` varchar
(255) NOT NULL DEFAULT '',
  `mqttp` varchar
(255) NOT NULL DEFAULT '',
  `apub` varchar
(255) NOT NULL DEFAULT '',
  `aprv` varchar
(255) NOT NULL DEFAULT '',
  `name` varchar
(255) NOT NULL DEFAULT '',
  `ip` varchar
(255) NOT NULL DEFAULT '',
  `mac` varchar
(255) NOT NULL DEFAULT '',
  `lt` varchar
(255) NOT NULL DEFAULT '',
  `lg` varchar
(255) NOT NULL DEFAULT '',
  `time` int
(11) NOT NULL DEFAULT '0',
  `cpu` decimal
(10,2) NOT NULL DEFAULT '0.00',
  `mem` decimal
(10,2) NOT NULL DEFAULT '0.00',
  `hdd` decimal
(10,2) NOT NULL DEFAULT '0.00',
  `tempr` decimal
(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttlz`
--

CREATE TABLE `mqttlz`
(
  `id` int
(11) NOT NULL,
  `uid` int
(11) NOT NULL DEFAULT '0',
  `lid` int
(11) NOT NULL DEFAULT '0',
  `zn` varchar
(255) NOT NULL,
  `time` int
(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttu`
--

CREATE TABLE `mqttu`
(
  `id` int
(11) NOT NULL,
  `uid` int
(11) NOT NULL DEFAULT '0',
  `lid` int
(11) NOT NULL DEFAULT '0',
  `zid` int
(11) NOT NULL DEFAULT '0',
  `did` int
(11) NOT NULL DEFAULT '0',
  `aid` int
(11) NOT NULL DEFAULT '0',
  `pid` int
(11) NOT NULL DEFAULT '0',
  `bid` int
(11) NOT NULL DEFAULT '0',
  `uname` varchar
(255) NOT NULL,
  `pw` varchar
(255) NOT NULL,
  `super` tinyint
(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttua`
--

CREATE TABLE `mqttua`
(
  `id` int
(11) NOT NULL,
  `uid` int
(11) NOT NULL DEFAULT '0',
  `lid` int
(11) NOT NULL DEFAULT '0',
  `zid` int
(11) NOT NULL DEFAULT '0',
  `did` int
(11) NOT NULL DEFAULT '0',
  `aid` int
(11) NOT NULL DEFAULT '0',
  `pid` int
(11) NOT NULL DEFAULT '0',
  `bid` int
(11) NOT NULL DEFAULT '0',
  `username` varchar
(255) NOT NULL,
  `topic` varchar
(255) NOT NULL,
  `rw` tinyint
(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients`
(
  `id` int
(11) NOT NULL,
  `lid` int
(11) NOT NULL DEFAULT '0',
  `aid` int
(11) NOT NULL DEFAULT '0',
  `name` varchar
(255) NOT NULL DEFAULT '',
  `email` varchar
(255) NOT NULL DEFAULT '',
  `username` varchar
(255) NOT NULL DEFAULT '',
  `password` varchar
(255) NOT NULL DEFAULT '',
  `pic` varchar
(255) NOT NULL DEFAULT 'default.png',
  `lt` varchar
(255) NOT NULL DEFAULT '',
  `lg` varchar
(255) NOT NULL DEFAULT '',
  `gpstime` int
(11) NOT NULL DEFAULT '0',
  `created` int
(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE `sensors`
(
  `id` int
(11) NOT NULL,
  `name` varchar
(255) NOT NULL,
  `type` varchar
(255) NOT NULL,
  `hasAction` tinyint
(4) NOT NULL DEFAULT '0',
  `hasCommand` tinyint
(4) NOT NULL DEFAULT '0',
  `image` varchar
(255) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings`
(
  `id` int
(11) NOT NULL,
  `aid` int
(11) NOT NULL,
  `version` varchar
(50) NOT NULL,
  `phpmyadmin` varchar
(255) NOT NULL,
  `recaptcha` varchar
(255) NOT NULL,
  `recaptchas` varchar
(255) NOT NULL,
  `gmaps` varchar
(255) NOT NULL,
  `lt` varchar
(255) NOT NULL,
  `lg` varchar
(255) NOT NULL,
  `meta_title` varchar
(255) NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `domainString` varchar
(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`
id`,
`aid`,
`version`,
`phpmyadmin`,
`recaptcha
`, `recaptchas`, `gmaps`, `lt`, `lg`, `meta_title`, `meta_description`, `meta_keywords`, `domainString`) VALUES
(1, 0, '0.3.0', 'phpmyadmin', '', '', '', '', 'HIAS Hospital Intelligent Automation System', 'Open-source Hospital Intelligent Automation System & Hospital Information/Management System. A locally hosted web/IoT server and proxy for managing a network of open-source, modular, intelligent devices, robotics and applications.', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tass`
--

CREATE TABLE `tass`
(
  `id` int
(11) NOT NULL,
  `type` varchar
(255) NOT NULL,
  `name` varchar
(255) NOT NULL,
  `uid` int
(11) NOT NULL DEFAULT '0',
  `lid` int
(11) NOT NULL DEFAULT '0',
  `zid` int
(11) NOT NULL DEFAULT '0',
  `did` int
(11) NOT NULL DEFAULT '0',
  `aid` int
(11) NOT NULL DEFAULT '0',
  `mid` int
(11) NOT NULL DEFAULT '0',
  `ip` varchar
(255) NOT NULL DEFAULT '',
  `mac` varchar
(255) NOT NULL DEFAULT '',
  `sport` varchar
(255) NOT NULL DEFAULT '',
  `sportf` varchar
(255) NOT NULL DEFAULT '',
  `sckport` varchar
(255) NOT NULL DEFAULT '',
  `strdir` varchar
(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users`
(
  `id` int
(11) NOT NULL,
  `lid` int
(11) NOT NULL DEFAULT '0',
  `aid` int
(11) NOT NULL DEFAULT '0',
  `admin` int
(11) NOT NULL DEFAULT '0',
  `username` varchar
(255) NOT NULL,
  `password` varchar
(255) NOT NULL,
  `pic` varchar
(255) NOT NULL DEFAULT 'default.png',
  `lt` varchar
(255) NOT NULL DEFAULT '',
  `lg` varchar
(255) NOT NULL DEFAULT '',
  `gpstime` int
(11) NOT NULL DEFAULT '0',
  `created` int
(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
ADD PRIMARY KEY
(`id`),
ADD KEY `gpstime`
(`gpstime`),
ADD KEY `lid`
(`lid`),
ADD KEY `zid`
(`zid`),
ADD KEY `did`
(`did`);

--
-- Indexes for table `blocked`
--
ALTER TABLE `blocked`
ADD PRIMARY KEY
(`id`),
ADD KEY `banned`
(`banned`);

--
-- Indexes for table `covid19data`
--
ALTER TABLE `covid19data`
ADD PRIMARY KEY
(`id`),
ADD KEY `country`
(`country`),
ADD KEY `province`
(`province`),
ADD KEY `timeadded`
(`timeadded`);

--
-- Indexes for table `covid19pulls`
--
ALTER TABLE `covid19pulls`
ADD PRIMARY KEY
(`id`);

--
-- Indexes for table `emar`
--
ALTER TABLE `emar`
ADD PRIMARY KEY
(`id`),
ADD KEY `uid`
(`uid`),
ADD KEY `lid`
(`lid`),
ADD KEY `zid`
(`zid`),
ADD KEY `did`
(`did`),
ADD KEY `aid`
(`aid`),
ADD KEY `mid`
(`mid`);

--
-- Indexes for table `logins`
--
ALTER TABLE `logins`
ADD PRIMARY KEY
(`id`);

--
-- Indexes for table `loginsf`
--
ALTER TABLE `loginsf`
ADD PRIMARY KEY
(`id`);

--
-- Indexes for table `mqtta`
--
ALTER TABLE `mqtta`
ADD PRIMARY KEY
(`id`),
ADD KEY `time`
(`time`),
ADD KEY `lid`
(`lid`),
ADD KEY `mqttu`
(`mqttu`),
ADD KEY `pid`
(`pid`);

--
-- Indexes for table `mqttl`
--
ALTER TABLE `mqttl`
ADD PRIMARY KEY
(`id`),
ADD KEY `time`
(`time`),
ADD KEY `apps`
(`apps`),
ADD KEY `name`
(`name`),
ADD KEY `devices`
(`devices`),
ADD KEY `zones`
(`zones`);

--
-- Indexes for table `mqttld`
--
ALTER TABLE `mqttld`
ADD PRIMARY KEY
(`id`),
ADD KEY `time`
(`time`),
ADD KEY `lid`
(`lid`),
ADD KEY `mqttu`
(`mqttu`),
ADD KEY `zid`
(`zid`),
ADD KEY `uid`
(`uid`),
ADD KEY `bid`
(`bid`);

--
-- Indexes for table `mqttlz`
--
ALTER TABLE `mqttlz`
ADD PRIMARY KEY
(`id`),
ADD KEY `uid`
(`uid`),
ADD KEY `lid`
(`lid`),
ADD KEY `time`
(`time`);

--
-- Indexes for table `mqttu`
--
ALTER TABLE `mqttu`
ADD PRIMARY KEY
(`id`),
ADD KEY `lid`
(`lid`),
ADD KEY `znid`
(`zid`),
ADD KEY `did`
(`did`),
ADD KEY `aid`
(`aid`),
ADD KEY `pid`
(`pid`),
ADD KEY `bid`
(`bid`);

--
-- Indexes for table `mqttua`
--
ALTER TABLE `mqttua`
ADD PRIMARY KEY
(`id`),
ADD KEY `lid`
(`lid`),
ADD KEY `zid`
(`zid`),
ADD KEY `did`
(`did`),
ADD KEY `aid`
(`aid`),
ADD KEY `uid`
(`uid`),
ADD KEY `lid_2`
(`lid`),
ADD KEY `zid_2`
(`zid`),
ADD KEY `did_2`
(`did`),
ADD KEY `aid_2`
(`aid`),
ADD KEY `pid`
(`pid`),
ADD KEY `bid`
(`bid`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
ADD PRIMARY KEY
(`id`),
ADD KEY `gpstime`
(`gpstime`);

--
-- Indexes for table `sensors`
--
ALTER TABLE `sensors`
ADD PRIMARY KEY
(`id`),
ADD KEY `hasAction`
(`hasAction`),
ADD KEY `hasCommand`
(`hasCommand`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
ADD PRIMARY KEY
(`id`),
ADD KEY `did`
(`aid`);

--
-- Indexes for table `tass`
--
ALTER TABLE `tass`
ADD PRIMARY KEY
(`id`),
ADD KEY `uid`
(`uid`),
ADD KEY `lid`
(`lid`),
ADD KEY `zid`
(`zid`),
ADD KEY `did`
(`did`),
ADD KEY `aid`
(`aid`),
ADD KEY `mid`
(`mid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY
(`id`),
ADD KEY `admin`
(`admin`),
ADD KEY `gpstime`
(`gpstime`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `blocked`
--
ALTER TABLE `blocked`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `covid19data`
--
ALTER TABLE `covid19data`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `covid19pulls`
--
ALTER TABLE `covid19pulls`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `emar`
--
ALTER TABLE `emar`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `loginsf`
--
ALTER TABLE `loginsf`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `mqtta`
--
ALTER TABLE `mqtta`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `mqttl`
--
ALTER TABLE `mqttl`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `mqttld`
--
ALTER TABLE `mqttld`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `mqttlz`
--
ALTER TABLE `mqttlz`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `mqttu`
--
ALTER TABLE `mqttu`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `mqttua`
--
ALTER TABLE `mqttua`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `sensors`
--
ALTER TABLE `sensors`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `tass`
--
ALTER TABLE `tass`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
