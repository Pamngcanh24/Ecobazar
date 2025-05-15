-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 15, 2025 lúc 06:15 AM
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
(5, 21, '2025-03-26 20:31:23', 350.00, 0.00, 0.00, 0.00, 4, 'Completed', 'ZaloPay', 'NgocAh', '', '', '', '', '', '', '', '2025-03-26 13:31:23'),
(11, 21, '2024-03-01 00:00:00', 135.00, 0.00, 0.00, 0.00, 5, 'Processing', 'ZaloPay', 'PhAh', '', '', '', '', '', '', '', '2025-03-26 16:05:21'),
(12, 21, '2023-05-15 10:00:00', 84.00, 100.00, 20.00, 4.00, 3, 'Completed', 'PayPal', 'John Doe', '196 cầu giấy', 'john@example.com', '1234567890', 'John Doe', '123 Main St, New York, NY 10001', 'john@example.com', '1234567890', '2025-03-27 16:40:16'),
(13, 21, '2025-03-31 09:03:13', 250.00, 250.00, 0.00, 0.00, 4, 'Completed', 'PayPal', 'PhNgahn', '44 trần thái tông, Phường Dịch Vọng Hậu', 'john@gmail.com', '1234567890', 'anhng', '123 Main St, New York, NY 10001', 'anhhn@gmai.com', '1234567890', '2025-03-31 02:03:13'),
(17, 25, '2025-05-13 20:58:53', 282.36, 282.36, 0.00, 0.00, 1, 'pending', 'bank', 'Giang', 'Trần Cung, Hà Nội', 'hgiang@gmail.com', '0911234562', 'Giang', 'Trần Cung, Hà Nội', 'hgiang@gmail.com', '0911234562', '2025-05-13 13:58:53'),
(18, 25, '2025-05-14 15:29:09', 29.49, 29.49, 0.00, 0.00, 1, 'pending', 'cod', 'moboo', 'Cổ Nhuế, BTL, HN', 'moboo@gmail.com', '0934267452', 'moboo', 'Cổ Nhuế, BTL, HN', 'moboo@gmail.com', '0934267452', '2025-05-14 08:29:09');

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
(1, 1, 1, 5, 14.00),
(2, 1, 2, 2, 14.00),
(3, 5, 3, 10, 26.70),
(4, 12, 2, 2, 50.00),
(5, 13, 2, 2, 50.00),
(6, 13, 3, 1, 150.00),
(7, 17, 10, 2, 14.99),
(8, 17, 2, 5, 14.00),
(9, 17, 1, 6, 14.00),
(10, 17, 3, 2, 26.70),
(11, 17, 5, 1, 15.00),
(12, 17, 6, 1, 14.99),
(13, 17, 14, 1, 14.99),
(14, 18, 7, 1, 14.50),
(15, 18, 15, 1, 14.99);

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
(1, 1, 'Red Capsicum', 14.00, 'capsicum-red.png', 'Fresh red capsicum', 25.00, 20),
(2, 1, 'Green Capsicum', 14.00, 'capsicum-green.png', 'Fresh green capsicum', 20.00, 5),
(3, 1, 'Green Chili', 26.70, 'chili-green.png', 'Spicy green chili', 30.50, 3),
(4, 1, 'Big Potatos', 14.99, 'potato.png', 'Big Potatos', 0.00, 0),
(5, 1, 'Chanise Cabbage', 15.00, 'cabbage.png', 'Chanise Cabbage', 0.00, 19),
(6, 1, 'Eggplant', 14.99, 'eggplant.png', 'Eggplant', 14.99, 19),
(7, 1, 'Ladies Finger', 14.50, 'corn.png', 'Ladies Finger', 14.50, 18),
(8, 1, 'Red Tomato', 16.00, 'tomato.png', 'Red Tomato', 16.00, 18),
(9, 1, 'Fresh Cauliflower', 14.99, 'cauliflower.png', 'Fresh Cauliflower', 14.99, 20),
(10, 2, 'Green Apple', 14.99, 'apple.png', 'Green Apple', 14.99, 18),
(11, 1, 'Fresh Cauliflower', 14.99, 'cauliflower.png', 'Fresh Cauliflower', 14.99, 20),
(12, 1, 'Green Cucumber', 14.99, 'cucumper.png', 'Green Cucumber', 14.99, 20),
(13, 2, 'Fresh Mango', 14.99, 'mango.png', 'Fresh Mango', 14.99, 0),
(14, 2, 'Green Littuce', 14.99, 'lettuce.png', 'Green Littuce', 14.99, 19),
(15, 1, 'Big Potatos', 14.99, 'potato.png', 'Big Potatos', 14.99, 19),
(16, 1, 'Green Chili', 14.99, 'chili-green.png', 'Green Chili', 14.99, 20),
(17, 2, 'Watermelon ', 19.00, 'watermelon.png', 'Watermelon ', NULL, 19);

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
(21, 'anhhn@gmail.com', '$2y$10$d2h.u38uvkYio2UgndkKp.lRRvUAidtnTnYWWDFjj0k/JQyFakP0.', '2025-03-25 09:28:38', 'd5d543e06c3ef7c866ed683b6108fa2720e85bc8367314b06b1badcadc0976bd', '2025-05-25 12:38:46', 'Ngoc Han', 'Pham', '0915417165'),
(22, 'giang@gmail.com', '$2y$10$XNaakrWGcMtCyyQqUysIaOACE0SwqQnUPmvX3KpZQu2Bosm8qGlSe', '2025-03-27 11:30:09', NULL, NULL, NULL, NULL, NULL),
(23, 'Hmai24@gmail.com', '$2y$10$a/ZOg3zbHgD2H1Zt7Fruw.aUN7yw2dm/OSs/0aQb8ftjUM5D/5K92', '2025-04-17 07:20:05', NULL, NULL, 'Huong Mai', 'Do', '01234567890'),
(24, 'tung@gg.c', '$2y$10$0kuP78qCKFJ0YfcZAsEP1O0ZPgXP7ibkaIbcl9LscGTh0uGPXLxzu', '2025-04-23 02:51:28', NULL, NULL, NULL, NULL, NULL),
(25, 'huonggiang@gmail.com', '$2y$10$fo8ytyPsUfwe187QloxUYe8XB6CpJYrHWgfvGel.mSh.D44QafbKO', '2025-05-10 23:25:05', '1c7628f4baa1a36aa1c1978d2f67a6687a85e3e79fb7ddf4c53420305e375327', '2025-06-13 11:45:25', NULL, NULL, NULL);

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
(13, 25, 8, '2025-05-14 08:40:01');

--
-- Chỉ mục cho các bảng đã đổ
--

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
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `thumbnails`
--
ALTER TABLE `thumbnails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
