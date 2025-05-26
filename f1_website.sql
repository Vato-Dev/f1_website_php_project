-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 02:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `f1_website`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-05-26 11:19:00');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('published','draft') DEFAULT 'published'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image_url`, `published_at`, `status`) VALUES
(1, 'F1 2024 Season Preview', 'The 2024 Formula 1 season promises to be one of the most exciting yet, with new regulations and fierce competition between the top teams. Red Bull Racing looks to continue their dominance, while Ferrari and Mercedes are working hard to close the gap.', 'https://via.placeholder.com/400x200/FF0000/FFFFFF?text=F1+2024', '2025-05-26 11:19:00', 'published'),
(2, 'New Aerodynamic Regulations', 'The FIA has introduced new aerodynamic regulations for the 2024 season aimed at improving overtaking opportunities and making the racing more competitive. Teams have been working tirelessly to adapt their cars to these changes.', 'https://via.placeholder.com/400x200/0000FF/FFFFFF?text=Aero+Rules', '2025-05-26 11:19:00', 'published'),
(3, 'Driver Market Updates', 'Several driver changes have been announced for the upcoming season. Young talents are getting their chance in F1, while experienced drivers are looking to prove themselves with new teams.', 'https://via.placeholder.com/400x200/00FF00/FFFFFF?text=Drivers', '2025-05-26 11:19:00', 'published');

-- --------------------------------------------------------

--
-- Table structure for table `races`
--

CREATE TABLE `races` (
  `id` int(11) NOT NULL,
  `race_name` varchar(100) NOT NULL,
  `circuit` varchar(100) NOT NULL,
  `country` varchar(50) NOT NULL,
  `race_date` date NOT NULL,
  `race_time` time NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `races`
--

INSERT INTO `races` (`id`, `race_name`, `circuit`, `country`, `race_date`, `race_time`, `status`, `created_at`) VALUES
(1, 'Bahrain Grand Prix', 'Bahrain International Circuit', 'Bahrain', '2024-03-02', '15:00:00', 'completed', '2025-05-26 11:19:00'),
(2, 'Saudi Arabian Grand Prix', 'Jeddah Corniche Circuit', 'Saudi Arabia', '2024-03-09', '17:00:00', 'completed', '2025-05-26 11:19:00'),
(3, 'Australian Grand Prix', 'Melbourne Grand Prix Circuit', 'Australia', '2024-03-24', '05:00:00', 'scheduled', '2025-05-26 11:19:00'),
(4, 'Japanese Grand Prix', 'Suzuka Circuit', 'Japan', '2024-04-07', '06:00:00', 'scheduled', '2025-05-26 11:19:00'),
(5, 'Chinese Grand Prix', 'Shanghai International Circuit', 'China', '2024-04-21', '08:00:00', 'scheduled', '2025-05-26 11:19:00');

-- --------------------------------------------------------

--
-- Table structure for table `race_results`
--

CREATE TABLE `race_results` (
  `id` int(11) NOT NULL,
  `race_id` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `driver_name` varchar(100) NOT NULL,
  `team` varchar(100) NOT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `race_results`
--

INSERT INTO `race_results` (`id`, `race_id`, `position`, `driver_name`, `team`, `points`) VALUES
(1, 1, 1, 'Max Verstappen', 'Red Bull Racing', 25),
(2, 1, 2, 'Sergio Perez', 'Red Bull Racing', 18),
(3, 1, 3, 'Charles Leclerc', 'Ferrari', 15),
(4, 2, 1, 'Max Verstappen', 'Red Bull Racing', 25),
(5, 2, 2, 'Charles Leclerc', 'Ferrari', 18),
(6, 2, 3, 'George Russell', 'Mercedes', 15);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `races`
--
ALTER TABLE `races`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `race_results`
--
ALTER TABLE `race_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `race_id` (`race_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `races`
--
ALTER TABLE `races`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `race_results`
--
ALTER TABLE `race_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `race_results`
--
ALTER TABLE `race_results`
  ADD CONSTRAINT `race_results_ibfk_1` FOREIGN KEY (`race_id`) REFERENCES `races` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
