-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 28, 2020 at 03:11 AM
-- Server version: 5.7.29-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `fhedfbvc`
--

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
  `lid` int(11) NOT NULL,
  `mqttu` varchar(255) NOT NULL,
  `mqttp` varchar(255) NOT NULL,
  `apub` varchar(255) NOT NULL,
  `aprv` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mqttl`
--

CREATE TABLE `mqttl` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
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
  `mqttu` varchar(255) NOT NULL,
  `mqttp` varchar(255) NOT NULL,
  `apub` varchar(255) NOT NULL,
  `aprv` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `time` int(11) NOT NULL
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
  `username` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `rw` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(1, '0.1.0', 'phpmyadmin', '', '', 'Peter Moss Hospital Intelligent Automation System', 'Open-source Hospital Intelligent Automation System & Hospital Information/Management System. A locally hosted web/IoT server and proxy for managing a network of open-soruce, modular, intelligent devices, robotics and applications.', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `admin` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

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
  ADD KEY `mqttu` (`mqttu`);

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
  ADD KEY `uid` (`uid`);

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
  ADD KEY `aid` (`aid`);

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
  ADD KEY `aid_2` (`aid`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin` (`admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blocked`
--
ALTER TABLE `blocked`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `loginsf`
--
ALTER TABLE `loginsf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `mqtta`
--
ALTER TABLE `mqtta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `mqttl`
--
ALTER TABLE `mqttl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `mqttld`
--
ALTER TABLE `mqttld`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `mqttlz`
--
ALTER TABLE `mqttlz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `mqttu`
--
ALTER TABLE `mqttu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `mqttua`
--
ALTER TABLE `mqttua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;