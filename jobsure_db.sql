-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 02:58 AM
-- Server version: 9.1.0
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jobsure_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `job_id` int NOT NULL,
  `status` varchar(50) DEFAULT 'Applied',
  `availability` varchar(255) DEFAULT NULL,
  `applied_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `job_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int NOT NULL,
  `listing_type` varchar(100) DEFAULT NULL,
  `job_title` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `pay_details` varchar(255) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `experience` varchar(100) DEFAULT NULL,
  `skills` text,
  `gender` varchar(50) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `process_details` text,
  `posted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `listing_type`, `job_title`, `company_name`, `location`, `pay_details`, `duration`, `qualification`, `experience`, `skills`, `gender`, `contact_person`, `contact_phone`, `process_details`, `posted_at`) VALUES
(5, 'job', 'React Developer', 'Shivora Ventures', 'Malad', '10000', NULL, 'Post-Graduate', '0-1 Year', 'HTML', 'Male Only', 'HR Veenit', '8104913374', '', '2025-11-18 11:59:07'),
(6, 'job', 'React Developer', 'Shivora Ventures', 'Malad', '10000', NULL, 'HSC', '0-1 Year', '', 'Male / Female', '', '', '', '2025-11-18 12:05:54'),
(7, 'job', 'React Developer', 'Shivora Ventures', 'Malad', '10000', NULL, 'HSC', '0-1 Year', '', 'Male / Female', '', '', '', '2025-11-18 12:09:15'),
(8, 'job', '.Net Developer', 'Shivora', 'MALAD', '6lpa', NULL, 'HSC', 'Fresher', '', 'Male / Female', '', '', '', '2025-11-18 12:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `user_id` int NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `skills` text,
  `work_experience` text,
  `currently_looking_for` varchar(255) DEFAULT NULL,
  `work_mode` varchar(255) DEFAULT NULL,
  `areas_of_interest` text,
  `resume_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`user_id`, `full_name`, `phone_no`, `location`, `gender`, `education`, `skills`, `work_experience`, `currently_looking_for`, `work_mode`, `areas_of_interest`, `resume_path`) VALUES
(7, NULL, '9594422839', 'MALAD', 'Male', 'Under Graduate', 'HTML,CSS,JS', 'Developer', 'Internships', 'In-office', 'Data Entry,Human Resources (HR),Software Development', 'uploads/resumes/user_7_691c75eed308e.pdf'),
(8, NULL, '8104913374', 'MALAD', 'Female', 'SSC', 'HTML,CSS,JS', '', 'Internships', 'In-office', 'Data Entry,Human Resources (HR),Software Development,General Management', 'uploads/resumes/user_8_691c96241848d.pdf'),
(9, NULL, '9594422839', 'Malad', 'Male', 'Graduate', 'HTML,CSS,JS', 'Fresher', 'Internships', 'In-office', 'Human Resources (HR),Data Entry', 'uploads/resumes/user_9_691c96aa9cda3.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_interviews`
--

CREATE TABLE `scheduled_interviews` (
  `id` int NOT NULL,
  `candidate_name` varchar(100) DEFAULT NULL,
  `candidate_email` varchar(100) DEFAULT NULL,
  `requested_time` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending_confirmation',
  `video_call_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `scheduled_interviews`
--

INSERT INTO `scheduled_interviews` (`id`, `candidate_name`, `candidate_email`, `requested_time`, `status`, `video_call_link`, `created_at`) VALUES
(1, 'Veenit', 'chauhanveenit16@gmail.com', '2025-11-18 10:00:00', 'pending_confirmation', NULL, '2025-11-17 09:50:41'),
(2, 'Rocky', 'RockyBhai71@gmail.com', '2025-11-18 10:00:00', 'pending_confirmation', NULL, '2025-11-17 10:07:49'),
(3, 'Veenit', 'chauhanveenit62@gmail.com', '2025-11-18 10:00:00', 'pending_confirmation', NULL, '2025-11-17 11:24:39'),
(4, 'Veenit', 'chauhanveenit62@gmail.com', '2025-11-18 10:00:00', 'pending_confirmation', NULL, '2025-11-17 12:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email_verified` tinyint(1) DEFAULT '0',
  `verification_token` varchar(255) DEFAULT NULL,
  `quiz_completed` tinyint(1) DEFAULT '0',
  `quiz_result` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `create_at`, `email_verified`, `verification_token`, `quiz_completed`, `quiz_result`) VALUES
(7, 'Mithilesh', 'xyz', 'mithilesh9324958141@gmail.com', '$2y$10$l2nu6A1rmPxMLHx74ZQcj.fXmUyTmXw6igb44PxAXzsWBEKdhz7s6', '2025-11-18 13:33:38', 1, NULL, 1, 'Technical'),
(8, 'Veenit', 'Jetani', 'krishamjetani@gmail.com', '$2y$10$4gCY0ipfz6bqn3L62uQeTOpFYCtcUQhSlPRIZN8DB2C45P97WSs4O', '2025-11-18 15:43:43', 1, NULL, 1, 'Management & Operations'),
(9, 'Veenit', 'Chauhan', 'chauhanveenit62@gmail.com', '$2y$10$GGJ5o9T5mtkS65f8CG9nouj9WBCDRRA3swjN6fSckhPI1g4UWopIa', '2025-11-18 15:53:39', 1, NULL, 1, 'Creative & Media');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `education` text,
  `skills` text,
  `experience` text,
  `preferences` text,
  `resume_filename` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_job_application_unique` (`user_id`,`job_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_job_unique` (`user_id`,`job_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `scheduled_interviews`
--
ALTER TABLE `scheduled_interviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `scheduled_interviews`
--
ALTER TABLE `scheduled_interviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
