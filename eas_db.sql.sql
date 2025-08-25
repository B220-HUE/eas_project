-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2025 at 10:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eas_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appraisals`
--

CREATE TABLE `appraisals` (
  `appraisal_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `cycle_id` int(11) NOT NULL,
  `evaluator_id` int(11) DEFAULT NULL,
  `appraisal_type` enum('Supervisor','Peer','Self') DEFAULT NULL,
  `appraisal_date` date DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appraisals`
--

INSERT INTO `appraisals` (`appraisal_id`, `employee_id`, `cycle_id`, `evaluator_id`, `appraisal_type`, `appraisal_date`, `comments`) VALUES
(1, 1, 0, 3, 'Supervisor', '2025-07-16', 'nnn'),
(2, 2, 0, 3, 'Supervisor', '2025-07-16', 'nnnn'),
(3, 2, 0, 3, 'Supervisor', '2025-07-18', 'improve in report accuracy'),
(6, 1, 6, NULL, 'Self', '2025-07-18', 'maintain the trend'),
(7, 1, 0, 3, 'Supervisor', '2025-08-05', 'improve on accuracy of reports'),
(8, 2, 5, NULL, 'Self', '2025-08-05', 'Designed intuitive dashboards for HR, supervisors, and employees, improving transparency in performance reviews.\r\n\r\n'),
(9, 1, 0, 3, 'Supervisor', '2025-08-20', 'improve on punctuality');

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_cycles`
--

CREATE TABLE `appraisal_cycles` (
  `cycle_id` int(11) NOT NULL,
  `cycle_name` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Open','Closed') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_history`
--

CREATE TABLE `appraisal_history` (
  `history_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `appraisal_id` int(11) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cycle`
--

CREATE TABLE `cycle` (
  `cycle_id` int(11) NOT NULL,
  `cycle_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Open','Closed') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cycle`
--

INSERT INTO `cycle` (`cycle_id`, `cycle_name`, `start_date`, `end_date`, `status`) VALUES
(1, 'Mid-Year Review 2025', '2025-07-01', '2025-07-31', 'Open'),
(2, 'Annual Review 2025', '2025-12-01', '2025-12-31', 'Closed'),
(3, 'Q1 Performance 2025', '2025-01-01', '2025-03-31', 'Open'),
(4, 'Q2 Performance 2025', '2025-04-01', '2025-06-30', 'Open'),
(5, 'Q3 Performance 2025', '2025-07-01', '2025-09-30', 'Open'),
(6, 'Q4 Performance 2025', '2025-10-01', '2025-12-31', 'Open'),
(7, 'Annual Review 2025', '2025-08-01', '2025-08-31', 'Open'),
(8, 'Annual Review 2025', '2025-07-05', '2025-07-18', 'Closed');

-- --------------------------------------------------------

--
-- Table structure for table `departmental_goals`
--

CREATE TABLE `departmental_goals` (
  `goal_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `cycle_id` int(11) NOT NULL,
  `goal_text` text NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Active',
  `target_date` date DEFAULT NULL,
  `goal_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dept_id`, `dept_name`, `description`) VALUES
(1, 'Human Resources', 'Handles employee affairs'),
(2, 'Nursing', 'Provides patient care'),
(3, 'Finance', 'Manages hospital finances'),
(4, 'Billing', 'Handles billing and payment processing'),
(5, 'Triage', 'Initial patient assessment and prioritization'),
(6, 'IT', 'Manages hospital technology and systems'),
(7, 'Lab Technician', 'Conducts lab tests and diagnostics'),
(8, 'Sonographer', 'Performs ultrasound imaging'),
(9, 'Radiographer', 'Performs X-ray and radiology procedures'),
(10, 'Pharmacy Technician', 'Dispenses and manages medications'),
(11, 'Procurement', 'Manages hospital supplies and purchasing'),
(12, 'Clinical Officer', 'Provides clinical care and diagnosis'),
(13, 'Records Officer', 'Manages patient records and documentation'),
(14, 'Customer Care', 'Handles patient inquiries and support');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `user_id`, `dept_id`, `job_title`, `hire_date`) VALUES
(1, 4, 2, 'Nurse', '2022-01-15'),
(2, 5, 3, 'Accountant', '2021-09-10');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `given_by` int(11) NOT NULL,
  `role` varchar(20) NOT NULL,
  `given_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `employee_id`, `feedback_text`, `given_by`, `role`, `given_at`, `status`) VALUES
(1, 4, 'mmmmmmmh', 2, 'HR', '2025-07-25 11:49:45', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_responses`
--

CREATE TABLE `feedback_responses` (
  `response_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `responder_id` int(11) NOT NULL,
  `response_text` text NOT NULL,
  `responded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `goal_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `cycle_id` int(11) NOT NULL,
  `goal_text` text NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `progress` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpis`
--

CREATE TABLE `kpis` (
  `kpi_id` int(11) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `kpi_name` varchar(100) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kpis`
--

INSERT INTO `kpis` (`kpi_id`, `dept_id`, `kpi_name`, `weight`, `description`) VALUES
(1, 2, 'Patient Satisfaction', 40.00, 'Feedback from patients'),
(2, 2, 'Timeliness', 30.00, 'Punctuality in shift reporting'),
(3, 3, 'Accuracy of Reports', 50.00, 'Financial report precision'),
(4, 3, 'Budget Compliance', 50.00, 'Staying within budget limits');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `requested_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `requested_at`) VALUES
(1, 'hr@eas.com', '2025-07-19 14:09:05'),
(2, 'hr@eas.com', '2025-07-19 14:09:21'),
(3, 'employee1@eas.com', '2025-07-19 14:09:42'),
(4, 'hr@eas.com', '2025-07-19 15:20:07'),
(5, 'employee1@eas.com', '2025-07-21 11:00:33'),
(6, 'employee1@eas.com', '2025-07-25 16:14:49'),
(7, 'employee1@eas.com', '2025-08-20 13:22:14');

-- --------------------------------------------------------

--
-- Table structure for table `rating_scale`
--

CREATE TABLE `rating_scale` (
  `scale_id` int(11) NOT NULL,
  `score_value` int(11) DEFAULT NULL,
  `definition` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating_scale`
--

INSERT INTO `rating_scale` (`scale_id`, `score_value`, `definition`) VALUES
(1, 1, 'Poor'),
(2, 2, 'Fair'),
(3, 3, 'Good'),
(4, 4, 'Very Good'),
(5, 5, 'Excellent');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `score_id` int(11) NOT NULL,
  `appraisal_id` int(11) DEFAULT NULL,
  `kpi_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`score_id`, `appraisal_id`, `kpi_id`, `rating`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 3),
(3, 1, 3, 5),
(4, 1, 4, 3),
(5, 2, 1, 3),
(6, 2, 2, 2),
(7, 2, 3, 3),
(8, 2, 4, 3),
(9, 3, 1, 3),
(10, 3, 2, 3),
(11, 3, 3, 3),
(12, 3, 4, 4),
(13, 6, 1, 3),
(14, 6, 2, 3),
(15, 6, 3, 3),
(16, 6, 4, 4),
(17, 7, 1, 1),
(18, 7, 2, 5),
(19, 7, 3, 3),
(20, 7, 4, 4),
(21, 8, 1, 5),
(22, 8, 2, 4),
(23, 8, 3, 4),
(24, 8, 4, 3),
(25, 9, 1, 3),
(26, 9, 2, 4),
(27, 9, 3, 1),
(28, 9, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `setting_key`, `setting_value`) VALUES
(1, 'min_kpi_score', '3'),
(2, 'max_kpi_score', '5'),
(3, 'appraisal_weight_supervisor', '60'),
(4, 'appraisal_weight_peer', '20'),
(5, 'appraisal_weight_self', '20'),
(6, 'default_cycle_status', 'Open'),
(7, 'enable_peer_review', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('Admin','HR','Supervisor','Employee') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin User', 'admin@eas.com', 'admin123', 'Admin', '2025-07-16 08:59:47'),
(2, 'HR Officer', 'hr@eas.com', 'hr123', 'HR', '2025-07-16 08:59:47'),
(3, 'Supervisor One', 'supervisor@eas.com', 'super123', 'Supervisor', '2025-07-16 08:59:47'),
(4, 'Employee One', 'employee1@eas.com', 'emp123', 'Employee', '2025-07-16 08:59:47'),
(5, 'Employee Two', 'employee2@eas.com', 'emp123', 'Employee', '2025-07-16 08:59:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appraisals`
--
ALTER TABLE `appraisals`
  ADD PRIMARY KEY (`appraisal_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `appraisal_cycles`
--
ALTER TABLE `appraisal_cycles`
  ADD PRIMARY KEY (`cycle_id`);

--
-- Indexes for table `appraisal_history`
--
ALTER TABLE `appraisal_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `appraisal_id` (`appraisal_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `cycle`
--
ALTER TABLE `cycle`
  ADD PRIMARY KEY (`cycle_id`);

--
-- Indexes for table `departmental_goals`
--
ALTER TABLE `departmental_goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `cycle_id` (`cycle_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `feedback_responses`
--
ALTER TABLE `feedback_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `feedback_id` (`feedback_id`),
  ADD KEY `responder_id` (`responder_id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `cycle_id` (`cycle_id`);

--
-- Indexes for table `kpis`
--
ALTER TABLE `kpis`
  ADD PRIMARY KEY (`kpi_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_scale`
--
ALTER TABLE `rating_scale`
  ADD PRIMARY KEY (`scale_id`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `appraisal_id` (`appraisal_id`),
  ADD KEY `kpi_id` (`kpi_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appraisals`
--
ALTER TABLE `appraisals`
  MODIFY `appraisal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `appraisal_cycles`
--
ALTER TABLE `appraisal_cycles`
  MODIFY `cycle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appraisal_history`
--
ALTER TABLE `appraisal_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cycle`
--
ALTER TABLE `cycle`
  MODIFY `cycle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `departmental_goals`
--
ALTER TABLE `departmental_goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback_responses`
--
ALTER TABLE `feedback_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpis`
--
ALTER TABLE `kpis`
  MODIFY `kpi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rating_scale`
--
ALTER TABLE `rating_scale`
  MODIFY `scale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appraisals`
--
ALTER TABLE `appraisals`
  ADD CONSTRAINT `appraisals_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `appraisals_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `appraisal_history`
--
ALTER TABLE `appraisal_history`
  ADD CONSTRAINT `appraisal_history_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `appraisal_history_ibfk_2` FOREIGN KEY (`appraisal_id`) REFERENCES `appraisals` (`appraisal_id`);

--
-- Constraints for table `departmental_goals`
--
ALTER TABLE `departmental_goals`
  ADD CONSTRAINT `departmental_goals_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`),
  ADD CONSTRAINT `departmental_goals_ibfk_2` FOREIGN KEY (`cycle_id`) REFERENCES `cycle` (`cycle_id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`);

--
-- Constraints for table `feedback_responses`
--
ALTER TABLE `feedback_responses`
  ADD CONSTRAINT `feedback_responses_ibfk_1` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`feedback_id`),
  ADD CONSTRAINT `feedback_responses_ibfk_2` FOREIGN KEY (`responder_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `goals_ibfk_2` FOREIGN KEY (`cycle_id`) REFERENCES `cycle` (`cycle_id`);

--
-- Constraints for table `kpis`
--
ALTER TABLE `kpis`
  ADD CONSTRAINT `kpis_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`);

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`appraisal_id`) REFERENCES `appraisals` (`appraisal_id`),
  ADD CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`kpi_id`) REFERENCES `kpis` (`kpi_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
