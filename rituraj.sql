-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 03, 2018 at 08:40 PM
-- Server version: 5.7.21-0ubuntu0.17.10.1
-- PHP Version: 7.1.11-0ubuntu0.17.10.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rituraj`
--

-- --------------------------------------------------------

--
-- Table structure for table `shopify_stores`
--

CREATE TABLE `shopify_stores` (
  `id` int(11) NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `shop` varchar(100) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `domain` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `address1` varchar(200) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `phone` int(10) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `latitude` varchar(100) DEFAULT NULL,
  `longitude` varchar(100) DEFAULT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `primary_location_id` varchar(200) DEFAULT NULL,
  `primary_locale` varchar(20) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `iana_timezone` varchar(100) DEFAULT NULL,
  `shop_owner` varchar(100) DEFAULT NULL,
  `province_code` varchar(10) DEFAULT NULL,
  `taxes_included` varchar(10) DEFAULT NULL,
  `tax_shipping` varchar(200) DEFAULT NULL,
  `county_taxes` varchar(10) DEFAULT NULL,
  `plan_display_name` varchar(50) DEFAULT NULL,
  `plan_name` varchar(50) DEFAULT NULL,
  `myshopify_domain` varchar(200) DEFAULT NULL,
  `google_apps_domain` varchar(200) DEFAULT NULL,
  `google_apps_login_enabled` varchar(100) DEFAULT NULL,
  `money_in_emails_format` varchar(50) DEFAULT NULL,
  `money_with_currency_in_emails_format` varchar(50) DEFAULT NULL,
  `eligible_for_payments` varchar(10) DEFAULT NULL,
  `requires_extra_payments_agreement` varchar(10) DEFAULT NULL,
  `password_enabled` varchar(10) DEFAULT NULL,
  `has_storefront` varchar(10) DEFAULT NULL,
  `customplex_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `code` text,
  `hmac` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_orders`
--

CREATE TABLE `tbl_orders` (
  `_id` int(11) NOT NULL,
  `orderNumber` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `zipcode` varchar(100) DEFAULT NULL,
  `total` varchar(100) DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `note` text,
  `fulfillments` varchar(100) DEFAULT NULL,
  `fraud` varchar(100) DEFAULT NULL,
  `paid` varchar(100) DEFAULT NULL,
  `card` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `orderid` varchar(100) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shopify_stores`
--
ALTER TABLE `shopify_stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD PRIMARY KEY (`_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shopify_stores`
--
ALTER TABLE `shopify_stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3037;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
