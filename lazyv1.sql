-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2024 at 03:38 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webshoplazyv1byjarvincenzo`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE `bank` (
  `id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `bnum` varchar(50) NOT NULL,
  `qrcode` varchar(250) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`id`, `fname`, `lname`, `bnum`, `qrcode`, `created_at`, `updated_at`) VALUES
(1, '#ชื่อ-ห้ามเว้นวรรค', '#นามสกุล-ห้ามเว้นวรรค', '#ธนาคาร / เลขบัญชี', '#ลิ้งค์รูปQrcode รับเงิน', '2023-02-11 07:48:46', '2024-05-26 12:19:46');

-- --------------------------------------------------------

--
-- Table structure for table `boxlog`
--

CREATE TABLE `boxlog` (
  `id` int(11) NOT NULL,
  `date` datetime(2) NOT NULL,
  `username` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `prize_name` varchar(255) NOT NULL,
  `uid` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `box_product`
--

CREATE TABLE `box_product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `des` varchar(1000) NOT NULL,
  `img` varchar(255) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0,
  `percent` int(3) NOT NULL DEFAULT 100,
  `salt_prize` varchar(255) NOT NULL DEFAULT 'ไม่ได้รับรางวัล',
  `c_type` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `box_product`
--

INSERT INTO `box_product` (`id`, `name`, `price`, `des`, `img`, `type`, `percent`, `salt_prize`, `c_type`) VALUES
(26, 'TEST PRODUCT 01', 99, 'TEST PRODUCT 01', 'https://cdn.discordapp.com/attachments/1243449178332598313/1244157351704002570/product.png?ex=66541753&is=6652c5d3&hm=d664404cdf6a8904c46e55aa166741e69cd072127e856a2f84d8119d2cd234eb&', 1, 100, 'ไม่ได้รับรางวัล', 'บริการกล่องสุ่ม'),
(33, 'TEST PRODUCT 02', 199, 'TEST PRODUCT 02', 'https://cdn.discordapp.com/attachments/1243449178332598313/1244157333735739442/zxnt2.png?ex=6654174f&is=6652c5cf&hm=a33588b102302959b0ea2098ef69c3c7e5ceaf1102a96d7d87c40eb8e7d2a447&', 0, 99, 'ไม่ได้รับรางวัล', 'TEST 01');

-- --------------------------------------------------------

--
-- Table structure for table `box_stock`
--

CREATE TABLE `box_stock` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` int(3) NOT NULL,
  `p_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `box_stock`
--

INSERT INTO `box_stock` (`id`, `username`, `password`, `p_id`) VALUES
(3, 'ทดสอบระบบ', 0, '33'),
(4, 'ทดสอบระบบ', 0, '33'),
(5, 'ทดสอบระบบ', 0, '33'),
(6, 'ทดสอบระบบ', 0, '33'),
(7, 'ทดสอบระบบ', 0, '33'),
(8, 'ทดสอบระบบ', 0, '33');

-- --------------------------------------------------------

--
-- Table structure for table `byshop`
--

CREATE TABLE `byshop` (
  `status` varchar(255) NOT NULL,
  `apikey` varchar(255) NOT NULL,
  `cost` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `byshop`
--

INSERT INTO `byshop` (`status`, `apikey`, `cost`) VALUES
('off', '#', '9');

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `id` int(11) NOT NULL,
  `link` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carousel`
--

INSERT INTO `carousel` (`id`, `link`) VALUES
(7, 'https://cdn.discordapp.com/attachments/1243449178332598313/1244159853451284520/ZXNT_STUDIO.png?ex=665419a8&is=6652c828&hm=bcc1b37be7d060b7a1da243efac7302a4807505928ea375e7d9fcd1bf4203ac8&');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `c_id` int(11) NOT NULL,
  `c_name` varchar(255) NOT NULL,
  `des` varchar(1000) NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`c_id`, `c_name`, `des`, `img`) VALUES
(5, 'TEST 01', ' ', 'https://cdn.discordapp.com/attachments/1243449178332598313/1244157351276318790/444204620_415557878133893_764781655258066581_n.png?ex=66541753&is=6652c5d3&hm=9daa7566024a3db5dd9dd61c69814e0ad009cb13e840614f5e801f138992f0a0&');

-- --------------------------------------------------------

--
-- Table structure for table `crecom`
--

CREATE TABLE `crecom` (
  `recom_1` int(11) NOT NULL DEFAULT 0,
  `recom_2` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crecom`
--

INSERT INTO `crecom` (`recom_1`, `recom_2`) VALUES
(5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `kbank_trans`
--

CREATE TABLE `kbank_trans` (
  `id` int(11) NOT NULL,
  `qr` varchar(255) NOT NULL,
  `ref` varchar(255) DEFAULT NULL,
  `sender` varchar(100) DEFAULT NULL,
  `date` datetime(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recom`
--

CREATE TABLE `recom` (
  `recom_1` int(11) NOT NULL DEFAULT 0,
  `recom_2` int(11) NOT NULL DEFAULT 0,
  `recom_3` int(11) NOT NULL DEFAULT 0,
  `recom_4` int(11) NOT NULL DEFAULT 0,
  `recom_5` int(11) NOT NULL DEFAULT 0,
  `recom_6` int(11) NOT NULL DEFAULT 0,
  `recom_7` int(11) NOT NULL DEFAULT 0,
  `recom_8` int(11) NOT NULL DEFAULT 0,
  `recom_9` int(11) NOT NULL DEFAULT 0,
  `recom_10` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recom`
--

INSERT INTO `recom` (`recom_1`, `recom_2`, `recom_3`, `recom_4`, `recom_5`, `recom_6`, `recom_7`, `recom_8`, `recom_9`, `recom_10`) VALUES
(26, 26, 26, 26, 23, 23, 23, 23, 23, 23);

-- --------------------------------------------------------

--
-- Table structure for table `redeem`
--

CREATE TABLE `redeem` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0,
  `max_count` int(11) NOT NULL,
  `prize` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `redeem_his`
--

CREATE TABLE `redeem_his` (
  `id` int(11) NOT NULL,
  `uid` varchar(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `date` datetime(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `wallet` varchar(255) NOT NULL,
  `fee` enum('on','off') NOT NULL DEFAULT 'off',
  `bg` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ann` varchar(255) NOT NULL,
  `main_color` varchar(255) NOT NULL,
  `sec_color` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `des` varchar(255) NOT NULL,
  `date` datetime(2) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `webhook_dc` varchar(255) NOT NULL,
  `bg_ann` varchar(500) NOT NULL,
  `tx_ann` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`wallet`, `fee`, `bg`, `name`, `ann`, `main_color`, `sec_color`, `contact`, `des`, `date`, `ip`, `logo`, `webhook_dc`, `bg_ann`, `tx_ann`) VALUES
('#', 'off', 'https://img5.pic.in.th/file/secure-sv1/bg-open4ef5d46b88a099c6.png', 'ZXNT Studio', '#', '#a5050a', '#801114', '#', '#', '2022-12-25 12:30:39.00', '::1', 'https://cdn.discordapp.com/attachments/1243449178332598313/1244157332582039595/zxnt.png?ex=6654174f&is=6652c5cf&hm=862a525c11cf3901623148da9d527e6acc43c393946c7f94ab01a089f81d7b69&', '#', 'https://cdn.discordapp.com/attachments/1243449178332598313/1244157351704002570/product.png?ex=66541753&is=6652c5d3&hm=d664404cdf6a8904c46e55aa166741e69cd072127e856a2f84d8119d2cd234eb&', '#'),
('#', 'off', 'https://img5.pic.in.th/file/secure-sv1/bg-open4ef5d46b88a099c6.png', 'ZXNT Studio', '#', '#a5050a', '#801114', '#', '#', '0000-00-00 00:00:00.00', '', 'https://cdn.discordapp.com/attachments/1243449178332598313/1244157332582039595/zxnt.png?ex=6654174f&is=6652c5cf&hm=862a525c11cf3901623148da9d527e6acc43c393946c7f94ab01a089f81d7b69&', '#', 'https://cdn.discordapp.com/attachments/1243449178332598313/1244157351704002570/product.png?ex=66541753&is=6652c5d3&hm=d664404cdf6a8904c46e55aa166741e69cd072127e856a2f84d8119d2cd234eb&', '#');

-- --------------------------------------------------------

--
-- Table structure for table `static`
--

CREATE TABLE `static` (
  `s_count` int(11) NOT NULL DEFAULT 2575,
  `b_count` int(11) NOT NULL DEFAULT 3525,
  `m_count` int(11) NOT NULL DEFAULT 5468,
  `last_change` datetime(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `static`
--

INSERT INTO `static` (`s_count`, `b_count`, `m_count`, `last_change`) VALUES
(0, 0, 0, '2024-05-26 20:09:33.00');

-- --------------------------------------------------------

--
-- Table structure for table `topup_his`
--

CREATE TABLE `topup_his` (
  `id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `amount` int(20) NOT NULL,
  `date` datetime NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(255) NOT NULL,
  `uimg` text NOT NULL,
  `ref` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `point` float NOT NULL,
  `total` float NOT NULL,
  `pin` varchar(6) NOT NULL,
  `profile` text DEFAULT 'https://kakarot.store/kakarot.png',
  `rank` int(1) NOT NULL DEFAULT 0,
  `accept` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `date`, `point`, `total`, `pin`, `profile`, `rank`, `accept`) VALUES
(1, 'Admin', '21232f297a57a5a743894a0e4a801fc3', '2024-05-26', 49403, 0, '', 'https://kakarot.store/kakarot.png', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `boxlog`
--
ALTER TABLE `boxlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `box_product`
--
ALTER TABLE `box_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `box_stock`
--
ALTER TABLE `box_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `kbank_trans`
--
ALTER TABLE `kbank_trans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redeem`
--
ALTER TABLE `redeem`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redeem_his`
--
ALTER TABLE `redeem_his`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topup_his`
--
ALTER TABLE `topup_his`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `boxlog`
--
ALTER TABLE `boxlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `box_product`
--
ALTER TABLE `box_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `box_stock`
--
ALTER TABLE `box_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kbank_trans`
--
ALTER TABLE `kbank_trans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `redeem`
--
ALTER TABLE `redeem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `redeem_his`
--
ALTER TABLE `redeem_his`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `topup_his`
--
ALTER TABLE `topup_his`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

-- --------------------------------------------------------

--
-- Table structure for table `game_sets`
--

CREATE TABLE `game_sets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `entry_cost` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `config` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_rewards`
--

CREATE TABLE `game_rewards` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `reward_type` varchar(32) NOT NULL DEFAULT 'text',
  `reward_value` varchar(500) DEFAULT NULL,
  `reward_amount` int(11) DEFAULT 0,
  `weight` int(11) NOT NULL DEFAULT 0,
  `color` varchar(32) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rule_value` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_logs`
--

CREATE TABLE `game_logs` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `game_type` varchar(32) NOT NULL,
  `entry_cost` int(11) NOT NULL,
  `choice_value` varchar(255) DEFAULT NULL,
  `system_value` varchar(255) DEFAULT NULL,
  `result_label` varchar(255) NOT NULL,
  `reward_type` varchar(32) NOT NULL,
  `reward_detail` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `game_sets`
--
ALTER TABLE `game_sets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_rewards`
--
ALTER TABLE `game_rewards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `game_logs`
--
ALTER TABLE `game_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for table `game_sets`
--
ALTER TABLE `game_sets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_rewards`
--
ALTER TABLE `game_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_logs`
--
ALTER TABLE `game_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
