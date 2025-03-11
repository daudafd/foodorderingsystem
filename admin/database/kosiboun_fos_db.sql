-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 10, 2025 at 06:51 AM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kosiboun_fos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `banner_images`
--

CREATE TABLE `banner_images` (
  `id` int(11) NOT NULL,
  `system_settings_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner_images`
--

INSERT INTO `banner_images` (`id`, `system_settings_id`, `image_path`) VALUES
(16, 1, '1740643680_1600654680_photo-1504674900247-0877df9cc836.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(30) NOT NULL,
  `client_ip` varchar(20) NOT NULL,
  `user_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `qty` int(30) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `size` varchar(10) DEFAULT NULL,
  `soup` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `client_ip`, `user_id`, `product_id`, `qty`, `price`, `size`, `soup`) VALUES
(436, '105.113.65.101', 0, 17, 1, 1300.00, NULL, 'Egunsi');

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`) VALUES
(1, 'Protein'),
(2, 'Beverages'),
(3, 'Best Sellers'),
(4, 'Meals'),
(10, 'Swallow');

-- --------------------------------------------------------

--
-- Table structure for table `guser`
--

CREATE TABLE `guser` (
  `id` int(11) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guser`
--

INSERT INTO `guser` (`id`, `google_id`, `email`, `first_name`, `last_name`, `created_at`) VALUES
(5, '117255118462061991746', 'daudafd@gmail.com', 'femi daniel', 'dauda', '2025-01-20 18:33:21');

-- --------------------------------------------------------

--
-- Table structure for table `meat_options`
--

CREATE TABLE `meat_options` (
  `id` int(10) NOT NULL,
  `meat_type` varchar(100) NOT NULL,
  `size` varchar(11) NOT NULL,
  `price` decimal(11,0) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meat_options`
--

INSERT INTO `meat_options` (`id`, `meat_type`, `size`, `price`) VALUES
(9, 'Chicken', 'small', 1500),
(4, 'Turkey', 'small', 3000),
(10, 'Chicken', 'medium', 1800),
(7, 'Goat Meat', 'small', 1500),
(8, 'Goat Meat', 'large', 2500),
(11, 'Chicken', 'large', 2000),
(12, 'Goat Meat', 'medium', 2000),
(13, 'Turkey', 'medium', 3500),
(14, 'Turkey', 'large', 4000),
(16, 'Okro', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(30) NOT NULL,
  `reference_id` varchar(100) NOT NULL,
  `user_id` int(10) NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `mobile` text NOT NULL,
  `email` text NOT NULL,
  `delivery_charge` varchar(10) DEFAULT NULL,
  `plastic_charge` varchar(10) DEFAULT NULL,
  `item_total` varchar(10) NOT NULL,
  `total_amount` varchar(10) DEFAULT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL,
  `delivery_type` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `payment_status` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `reference_id`, `user_id`, `name`, `address`, `mobile`, `email`, `delivery_charge`, `plastic_charge`, `item_total`, `total_amount`, `transaction_reference`, `delivery_type`, `created_at`, `confirmed_at`, `payment_status`) VALUES
(117, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '1300', NULL, '8800', '10100', 'transfer', 0, '2025-03-07 08:43:15', '2025-03-07 09:29:18', 0),
(118, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '0', NULL, '8800', '8800', 'transfer', 0, '2025-03-07 08:51:43', '2025-03-07 09:29:18', 0),
(119, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '1100', NULL, '3800', '4900', 'transfer', 0, '2025-03-07 08:57:48', '2025-03-07 09:29:18', 0),
(120, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '0', NULL, '3300', '3300', 'transfer', 0, '2025-03-07 09:53:06', '2025-03-07 09:53:06', 1),
(121, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '0', NULL, '12200', '12200', 'transfer', 0, '2025-03-07 09:18:32', '2025-03-07 09:29:18', 0),
(122, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '1500', NULL, '5000', '6500', 'transfer', 0, '2025-03-07 09:42:49', '2025-03-07 09:42:49', 2),
(123, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '0', NULL, '8800', '8800', 'transfer', 0, NULL, NULL, 0),
(124, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '1100', NULL, '1800', '2900', 'transfer', 0, '2025-03-07 09:48:39', '2025-03-07 09:48:39', 2),
(125, '', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '0', NULL, '200', '200', 'transfer', 0, '2025-03-07 09:59:43', '2025-03-07 10:00:30', 1),
(126, 'FIFI-67CAB902ABA32', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '0', NULL, '1300', '1300', 'transfer', 0, '2025-03-07 10:14:42', '2025-03-08 05:39:25', 1),
(127, '20250307372093', 30, 'Jumoke Ogidan', 'High school', '0801234567', 'jummy@gmail.com', '0', NULL, '1200', '1200', 'transfer', 0, '2025-03-07 10:18:46', '2025-03-07 10:22:52', 3);

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int(30) NOT NULL,
  `order_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `qty` int(30) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `size` varchar(10) DEFAULT NULL,
  `soup` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `order_id`, `product_id`, `qty`, `price`, `size`, `soup`) VALUES
(165, 117, 18, 1, 8800.00, '0', NULL),
(166, 118, 18, 1, 8800.00, 'medium', NULL),
(167, 119, 20, 1, 2500.00, 'large', NULL),
(168, 119, 17, 1, 1300.00, '', NULL),
(169, 120, 20, 1, 2000.00, 'medium', NULL),
(170, 120, 17, 1, 1300.00, '', NULL),
(171, 121, 17, 2, 1300.00, '', 'Egunsi'),
(172, 121, 18, 1, 8800.00, 'medium', ''),
(173, 121, 16, 1, 800.00, '', ''),
(174, 122, 20, 2, 2500.00, 'large', ''),
(175, 123, 18, 1, 8800.00, 'medium', ''),
(176, 124, 18, 1, 1800.00, 'medium', ''),
(177, 125, 40, 1, 200.00, '', ''),
(178, 126, 17, 1, 1300.00, '', 'Vegetables'),
(179, 127, 45, 1, 1200.00, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `id` int(10) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `img_path` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0= unavailable, 1 Available',
  `price_small` decimal(10,2) DEFAULT NULL,
  `price_medium` decimal(10,2) DEFAULT NULL,
  `price_large` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`id`, `category_id`, `name`, `description`, `price`, `img_path`, `status`, `price_small`, `price_medium`, `price_large`) VALUES
(4, 4, 'Jollof rice', ' Experience the rich, smoky flavors of our perfectly cooked party Jollof Rice, the heart of every celebration.', 800.00, '1733677260_jollof.jpeg', 1, NULL, NULL, NULL),
(16, 4, 'Stir Fry Spaghetti', ' Indulge in the classic comfort of our Spaghetti, prepared with a touch of our unique culinary flair.', 800.00, '1733679000_spag.jpg', 1, NULL, NULL, NULL),
(17, 10, 'Semo', 'A smooth and satisfying staple, our Semo is the perfect complement to any savory dish.', 1300.00, '1733679060_semo.jpg', 1, NULL, NULL, NULL),
(18, 3, 'Chicken', ' Succulent and flavorful, our Chicken is prepared to perfection, offering a taste of pure delight.', 1500.00, '1733679120_chicken.jpg', 1, 1500.00, 1800.00, 2000.00),
(19, 3, 'Turkey', 'Tender and juicy, our Turkey is a true delicacy, bursting with savory goodness.', 3000.00, '1733679120_turkey.jpeg', 1, 3000.00, 3500.00, 4000.00),
(20, 3, 'Goat Meat', 'Rich and aromatic, our Goat Meat is a culinary masterpiece, full of authentic flavors.', 1500.00, '1733679120_goat.jpg', 1, 1500.00, 2000.00, 2500.00),
(21, 3, 'Beef', ' Savor the robust and satisfying taste of our Beef, cooked to tender perfection.', 1000.00, '1733679180_beef.jpg', 1, NULL, NULL, NULL),
(22, 3, 'Coleslaw', 'A crisp and refreshing complement, our Coleslaw adds a touch of lightness to your meal.', 400.00, '1733679180_coleslaw.jpg', 1, NULL, NULL, NULL),
(40, 3, 'Dodo', 'A taste of home, ripe plantain fried to a golden brown.', 200.00, '1740993900_dodo.jpeg', 1, NULL, NULL, NULL),
(42, 4, 'Fried Rice', 'A vibrant medley of flavors and textures, our Fried Rice is a delightful culinary adventure.', 800.00, '1740994200_friedrice.jpg', 1, NULL, NULL, NULL),
(43, 10, 'Poundo Yam', 'Freshly prepared poundo yam, a hearty and satisfying accompaniment.', 1500.00, '1740994500_poundo-yam.jpg', 1, NULL, NULL, NULL),
(44, 10, 'Yellow Garri', 'A vibrant yellow swallow, prepared with garri, ideal for soaking up flavorful soups.', 1000.00, '1740994560_eba.jpeg', 0, NULL, NULL, NULL),
(45, 3, 'Titus Fish', 'Titus fish, marinated in a blend of traditional Nigerian spices and fried to a flavorful crisp.', 1200.00, '1740994740_titus.jpeg', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `soup_options`
--

CREATE TABLE `soup_options` (
  `id` int(11) NOT NULL,
  `soup_type` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `soup_options`
--

INSERT INTO `soup_options` (`id`, `soup_type`) VALUES
(1, 'Vegetables'),
(2, 'Egunsi');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_img`, `about_content`) VALUES
(1, 'FIFI Cuisine', 'admin@fificuisine.com', '070623197789', '1734904440_fifi 2.jpg', '<p style=\"text-align: center; background: transparent; position: relative;\"><span style=\"color: rgb(0, 0, 0); font-family: \"Open Sans\", Arial, sans-serif; margin: 0px; padding: 0px; text-align: justify;\"><span style=\"font-weight: bolder;\"><span style=\"color: rgb(0, 0, 0); font-family: \"Open Sans\", Arial, sans-serif; margin: 0px; padding: 0px;\">FIFIï¿½s Cuisine</span>!</span><br></span></p><p style=\"text-align: center; background: transparent; position: relative;\"><br></p><p>At <b>FIFIï¿½s Cuisine</b>, we are passionate about bringing authentic and delicious food to your table. Our cuisine is a delightful fusion of tradition and innovation, where every dish is crafted with the finest ingredients, attention to detail, and a love for bold flavors. Whether youï¿½re craving comforting classics or exciting new tastes, we promise to offer a unique culinary experience that celebrates diverse cultures and rich flavors.</p><p><br></p><p>Our chefs, who come from a variety of backgrounds, infuse every recipe with their expertise and passion. From our carefully selected spices to our hand-prepared dishes, each meal is a celebration of the artistry of cooking. We strive to make every visit memorable, offering a warm atmosphere and exceptional service, ensuring that every guest feels like part of our family.</p><p><br></p><p><i style=\"font-weight: bolder;\">ï¿½Join us atï¿½<span style=\"text-align: justify;\"><span style=\"color: rgb(0, 0, 0); font-family: \"Open Sans\", Arial, sans-serif; margin: 0px; padding: 0px;\">FIFIï¿½s Cuisine</span>ï¿½for an unforgettable dining experience where every bite tells a story.</span></i></p><p></p>');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=admin , 2 = staff',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`, `profile_picture`) VALUES
(1, 'Administrator', 'admin', '$2y$10$i1nx5DxmFCbxpw8vr9Ea9O.aqmzKPUAkMHsAnK.z28XyYjhvDtqLK', 1, NULL),
(17, 'femi', 'dfd', '$2y$10$AbRFZSO/FF4tYw1a51iUpe9thPPalsOBnZGvRY80F/7kBVR9EHkRW', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `user_id` int(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `mobile` varchar(10) DEFAULT NULL,
  `address` varchar(300) DEFAULT NULL,
  `login_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_id`, `first_name`, `last_name`, `email`, `password`, `google_id`, `image_url`, `mobile`, `address`, `login_type`, `created_at`) VALUES
(28, 'femi', 'dauda', 'daudafd@gmail.com', '$2y$10$5akvUv0TAI18AoH5ovPbUuG3a99vgS74aisOC7ZrT7MDHdscH4vf.', NULL, NULL, '0706420395', 'Opp. Jaiz bank', NULL, '2025-02-02 22:56:00'),
(30, 'Jumoke', 'Ogidan', 'jummy@gmail.com', '$2y$10$RG4zwgh9UfAxQY3LwqY7K.pQnvA7R2FokfK6FCa4AY0wbKLavK8OG', NULL, NULL, '0801234567', 'High school', NULL, '2025-02-27 08:13:25'),
(31, 'paulhus', 'steven', 'paulhussteven@gmail.com', '', '110547111678599340328', NULL, NULL, NULL, '3', '2025-02-27 08:59:23'),
(32, 'lekan', 'Dauda', 'olamilekan@gmail.com', '$2y$10$Uk2KD413Mck7xVqSeJeDgOC5kiSxxDROOXZ/FNiLgvTafZ5CNunCm', NULL, NULL, '0809876543', 'Ijoka', NULL, '2025-02-27 16:22:35'),
(33, 'steven', 'paulhus', 'jummy1@gmail.com', '$2y$10$/g/WdjiyDn9iAjq25XBTc.hCVgeoerB0CWkyJ6uVXh.S06mQILxs.', NULL, NULL, '9289005174', '500 HAVENSIDE AVE', NULL, '2025-03-08 06:42:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banner_images`
--
ALTER TABLE `banner_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_settings_id` (`system_settings_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guser`
--
ALTER TABLE `guser`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- Indexes for table `meat_options`
--
ALTER TABLE `meat_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_meat_type` (`meat_type`),
  ADD KEY `idx_meat_size` (`size`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `password_resets_ibfk_1` (`user_id`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_product_name` (`name`);

--
-- Indexes for table `soup_options`
--
ALTER TABLE `soup_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner_images`
--
ALTER TABLE `banner_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=437;

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `guser`
--
ALTER TABLE `guser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meat_options`
--
ALTER TABLE `meat_options`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `soup_options`
--
ALTER TABLE `soup_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `banner_images`
--
ALTER TABLE `banner_images`
  ADD CONSTRAINT `banner_images_ibfk_1` FOREIGN KEY (`system_settings_id`) REFERENCES `system_settings` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
