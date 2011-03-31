-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: 10.6.186.64
-- Generation Time: Mar 31, 2011 at 12:43 PM
-- Server version: 5.0.91
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `justnearme`
--

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE `codes` (
  `id` varchar(1000) NOT NULL,
  `image` varchar(1000) NOT NULL,
  `locationname` varchar(1000) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `userid` varchar(1000) NOT NULL,
  `username` varchar(1000) NOT NULL,
  `firstname` varchar(1000) NOT NULL,
  `note` varchar(10000) NOT NULL,
  `lat` varchar(100) NOT NULL,
  `lng` varchar(100) NOT NULL,
  `time` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tweets`
--

CREATE TABLE `tweets` (
  `date` datetime NOT NULL,
  `lat` varchar(1000) NOT NULL,
  `lng` varchar(1000) NOT NULL,
  `loc` varchar(1000) NOT NULL,
  `tweet` varchar(5000) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `ip` varchar(1000) NOT NULL,
  `query` varchar(1000) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
