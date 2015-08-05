-- phpMyAdmin SQL Dump
-- version 4.3.11.1
-- http://www.phpmyadmin.net
--
-- Host: 192.168.0.22:3306
-- Generation Time: Jul 17, 2015 at 04:31 PM
-- Server version: 5.5.43-0ubuntu0.14.04.1
-- PHP Version: 5.5.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev_gmf`
--

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE IF NOT EXISTS `order_list` (
  `id` int(11) NOT NULL,
  `order_id` varchar(40) NOT NULL,
  `stock_code` char(6) NOT NULL,
  `stock_name` char(16) NOT NULL,
  `order_price` decimal(10,0) NOT NULL,
  `order_amount` decimal(10,0) NOT NULL,
  `order_status` int(11) NOT NULL,
  `order_time` int(11) NOT NULL,
  `buyorsell` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `uid` int(11) NOT NULL,
  `gid` varchar(10) NOT NULL,
  `updated_at` datetime NOT NULL,
  `status_name` varchar(16) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_trade_list`
--

CREATE TABLE IF NOT EXISTS `user_trade_list` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `gid` varchar(10) NOT NULL,
  `stock_code` varchar(6) NOT NULL,
  `stock_amount` decimal(10,0) NOT NULL,
  `enable_amount` decimal(10,0) NOT NULL,
  `cost_price` decimal(10,0) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `last_amount` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '昨日股票持有量',
  `last_price` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '昨日价格'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`), ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `user_trade_list`
--
ALTER TABLE `user_trade_list`
  ADD PRIMARY KEY (`id`), ADD KEY `uid` (`uid`,`gid`,`stock_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `user_trade_list`
--
ALTER TABLE `user_trade_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
