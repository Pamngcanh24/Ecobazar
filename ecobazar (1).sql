-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 18, 2025 lúc 02:30 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ecobazar`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `remember_token`, `token_expiry`, `created_at`) VALUES
(1, 'admin@gmail.com', '$2y$10$nTAK6Zx6KDi7R64Xsr2KHuTgfgfb7X3eZCLnKsClAEmrCAqLekYZi', 'a176cf58ce17b293f63d48049aa80212d407efd0accd19f42892d5462184ae4e', '2025-06-15 03:51:16', '2025-05-16 01:45:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`) VALUES
(1, 'Vegetables', 'vegetables.png'),
(2, 'Fruit', 'fruit.png'),
(3, 'Meat & Fish', 'meat.png'),
(4, 'Snacks', 'snacks.png'),
(5, 'Beverages', 'beverage.png'),
(6, 'Beauty & Health', 'beauty.png'),
(7, 'Bread & Bakery', 'bread.png'),
(8, 'Baking Needs', 'baking.png'),
(9, 'Cooking', 'cooking.png'),
(10, 'Diabetic Food', 'diabetic.png'),
(11, 'Dish Detergents', 'ditergent.png'),
(12, 'Oil', 'oil.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) DEFAULT 0.00,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `product_count` int(11) DEFAULT 1,
  `status` varchar(50) DEFAULT 'Processing',
  `payment_method` varchar(50) NOT NULL,
  `billing_name` varchar(100) NOT NULL,
  `billing_address` text NOT NULL,
  `billing_email` varchar(100) NOT NULL,
  `billing_phone` varchar(20) NOT NULL,
  `shipping_name` varchar(100) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_email` varchar(100) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `total`, `subtotal`, `discount`, `shipping_cost`, `product_count`, `status`, `payment_method`, `billing_name`, `billing_address`, `billing_email`, `billing_phone`, `shipping_name`, `shipping_address`, `shipping_email`, `shipping_phone`, `created_at`) VALUES
(24, 28, '2025-05-16 15:15:23', 41.69, 41.69, 0.00, 0.00, 1, 'pending', 'cod', 'Anh', 'Trần Cung, HN', 'anhhn@gmail.com', '0923456342', 'Anh', 'Trần Cung, HN', 'anhhn@gmail.com', '0923456342', '2025-05-16 08:15:23'),
(25, 28, '2025-05-16 15:16:52', 14.00, 14.00, 0.00, 0.00, 1, 'completed', 'bank_transfer', 'kevil', 'Hoàng Mai, HN', 'kevil@gmail.com', '0923456342', 'Kevil', 'Hoàng Mai, HN', 'kevil@gmail.com', '0923456342', '2025-05-16 08:16:52'),
(27, 24, '2025-05-16 19:08:04', 34.00, 34.00, 0.00, 0.00, 1, 'pending', 'cod', 'Pham Ngoc Anh', '44 tran thai tong', 'anhhn@gmail.com', '1234566789', 'Pham Ngoc Anh', '44 tran thai tong', 'anhhn@gmail.com', '1234566789', '2025-05-16 12:08:04'),
(30, 24, '2025-05-17 17:00:33', 11.00, 11.00, 0.00, 0.00, 1, 'pending', 'bank', 'Pham Ngoc Anh', 'Hà nội', 'anhhn@gmail.com', '1234566789', 'Pham Ngoc Anh', 'Hà nội', 'anhhn@gmail.com', '1234566789', '2025-05-17 10:00:33'),
(31, 24, '2025-05-17 17:05:34', 14.00, 14.00, 0.00, 0.00, 1, 'pending', 'bank', 'Pham Ngoc Anh', 'Mai Dịch', 'anhhn@gmail.com', '1234566789', 'Pham Ngoc Anh', 'Mai Dịch', 'anhhn@gmail.com', '1234566789', '2025-05-17 10:05:34'),
(33, 24, '2025-05-17 17:11:15', 55.20, 55.20, 0.00, 0.00, 1, 'pending', 'bank', 'Pham Ngoc Anh', '44 Trần Thái Tông', 'anhhh@gmail.com', '1234566789', 'Pham Ngoc Anh', '44 Trần Thái Tông', 'anhhh@gmail.com', '1234566789', '2025-05-17 10:11:15'),
(34, 24, '2025-05-17 17:36:35', 14.99, 14.99, 0.00, 0.00, 1, 'pending', 'bank', 'Pham Ngoc Anh', 'Nam Nan', 'anhhn@gmail.com', '1234566789', 'Pham Ngoc Anh', 'Nam Nan', 'anhhn@gmail.com', '1234566789', '2025-05-17 10:36:35'),
(35, 24, '2025-05-17 17:38:05', 34.00, 34.00, 0.00, 0.00, 1, 'pending', 'cod', 'Pham Ngoc Anh', '12 Cau Giay', 'anhhn@gmail.com', '1234566789', 'Pham Ngoc Anh', '12 Cau Giay', 'anhhn@gmail.com', '1234566789', '2025-05-17 10:38:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(16, 20, 3, 1, 26.70),
(17, 20, 7, 1, 14.50),
(18, 20, 8, 1, 16.00),
(19, 20, 17, 1, 19.00),
(20, 24, 3, 1, 26.70),
(21, 24, 6, 1, 14.99),
(22, 25, 2, 1, 14.00),
(23, 26, 5, 1, 15.00),
(24, 26, 7, 1, 14.50),
(25, 27, 1, 1, 34.00),
(26, 30, 18, 1, 11.00),
(27, 31, 2, 1, 14.00),
(28, 33, 2, 1, 14.00),
(29, 33, 7, 1, 14.50),
(30, 33, 3, 1, 26.70),
(31, 34, 15, 1, 14.99),
(32, 35, 1, 1, 34.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `image`, `description`, `old_price`, `stock`) VALUES
(1, 1, 'Red Capsicum', 34.00, 'capsicum-red.png', 'Fresh red capsicum', 30.00, 28),
(2, 1, 'Green Capsicum', 14.00, 'capsicum-green.png', 'Fresh green capsicum', 20.00, 2),
(3, 1, 'Green Chili', 26.70, 'chili-green.png', 'Spicy green chili', 30.50, 5),
(4, 1, 'Big Potatos', 14.99, 'potato.png', 'Big Potatos', 0.00, 0),
(5, 1, 'Chanise Cabbage', 15.00, 'cabbage.png', 'Chanise Cabbage', 0.00, 18),
(6, 1, 'Eggplant', 14.99, 'eggplant.png', 'Eggplant', 14.99, 18),
(7, 1, 'Ladies Finger', 14.50, 'corn.png', 'Ladies Finger', 14.50, 15),
(8, 1, 'Red Tomato', 16.00, 'tomato.png', 'Red Tomato', 16.00, 17),
(9, 1, 'Fresh Cauliflower', 14.99, 'cauliflower.png', 'Fresh Cauliflower', 14.99, 20),
(10, 2, 'Green Apple', 14.99, 'apple.png', 'Green Apple', 14.99, 18),
(12, 1, 'Green Cucumber', 14.99, 'cucumper.png', 'Green Cucumber', 14.99, 20),
(13, 2, 'Fresh Mango', 14.99, 'mango.png', 'Fresh Mango', 14.99, 0),
(14, 2, 'Green Littuce', 14.99, 'lettuce.png', 'Green Littuce', 14.99, 19),
(15, 1, 'Big Potatos', 14.99, 'potato.png', 'Big Potatos', 14.99, 18),
(16, 1, 'Green Chili', 14.99, 'chili-green.png', 'Green Chili', 14.99, 20),
(17, 2, 'Watermelon ', 19.00, 'watermelon.png', 'Watermelon ', 19.00, 18),
(18, 2, 'Orange juice', 11.00, 'orange.png', 'Orange juice', 10.00, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thumbnails`
--

CREATE TABLE `thumbnails` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `thumbnail_image` varchar(255) NOT NULL,
  `position` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thumbnails`
--

INSERT INTO `thumbnails` (`id`, `product_id`, `thumbnail_image`, `position`) VALUES
(1, 5, 'thumbnails1.png', 1),
(2, 5, 'thumbnails2.png', 2),
(3, 5, 'thumbnails3.png', 3),
(4, 5, 'thumbnails4.png', 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`, `remember_token`, `token_expiry`, `first_name`, `last_name`, `phone`) VALUES
(21, 'test@gmail.com', '$2y$10$d2h.u38uvkYio2UgndkKp.lRRvUAidtnTnYWWDFjj0k/JQyFakP0.', '2025-03-25 09:28:38', 'd5d543e06c3ef7c866ed683b6108fa2720e85bc8367314b06b1badcadc0976bd', '2025-05-25 12:38:46', 'Ngoc Han', 'Pham', '0915417165'),
(23, 'Hmai24@gmail.com', '$2y$10$a/ZOg3zbHgD2H1Zt7Fruw.aUN7yw2dm/OSs/0aQb8ftjUM5D/5K92', '2025-04-17 07:20:05', NULL, NULL, 'Huong Mai', 'Do', '01234567890'),
(24, 'anhhn@gmail.com', '$2y$10$776nYMdNSmZFIs9ZIV72uuTzuTd1E/t4UsR0oMF7mJ7T7p.52RANq', '2025-04-23 02:51:28', 'd878b44db1d96650b8891d423f7ecbd063124c488051cc87302659c7fb30cd5e', '2025-06-16 12:10:41', 'Ngọc Ánh', 'Phạm', '0911231123'),
(28, 'huonggiang@gmail.com', '$2y$10$qhLvgc0P7FnG8irLVxqa/eksIZYrm7IrqRGv6cZWuxv/bQNRYRh2e', '2025-05-16 07:25:17', NULL, NULL, 'Hương Giang', 'Nguyễn ', '0198765432');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 21, 1, '2025-04-12 07:33:14'),
(2, 21, 2, '2025-04-12 08:15:58'),
(12, 25, 4, '2025-05-14 08:35:16'),
(13, 25, 8, '2025-05-14 08:40:01'),
(17, 24, 3, '2025-05-16 12:07:19'),
(18, 24, 1, '2025-05-17 13:58:12'),
(19, 24, 7, '2025-05-17 13:58:14'),
(20, 24, 15, '2025-05-17 13:58:16'),
(21, 24, 13, '2025-05-17 13:59:46');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Chỉ mục cho bảng `thumbnails`
--
ALTER TABLE `thumbnails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `thumbnails`
--
ALTER TABLE `thumbnails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `thumbnails`
--
ALTER TABLE `thumbnails`
  ADD CONSTRAINT `thumbnails_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
