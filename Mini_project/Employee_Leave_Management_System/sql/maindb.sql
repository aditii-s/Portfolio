-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2025 at 07:36 PM
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
-- Database: `maindb`
--

-- --------------------------------------------------------

--
-- Table structure for table `hod`
--

CREATE TABLE `hod` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hod`
--

INSERT INTO `hod` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'hod', '17d84f171d54c301fabae1391a125c4e', '2024-07-01 11:42:58');

-- --------------------------------------------------------

--
-- Table structure for table `principal`
--

CREATE TABLE `principal` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `principal`
--

INSERT INTO `principal` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'principal', 'e7d715a9b79d263ae527955341bbe9b1', '2024-07-01 11:42:58');

-- --------------------------------------------------------

--
-- Table structure for table `tblclassrequests`
--

CREATE TABLE `tblclassrequests` (
  `id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `requester_name` varchar(100) NOT NULL,
  `requester_email` varchar(100) DEFAULT NULL,
  `requested_emp_id` int(11) NOT NULL,
  `requested_emp_name` varchar(100) NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `response_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblclassrequests`
--

INSERT INTO `tblclassrequests` (`id`, `requester_id`, `requester_name`, `requester_email`, `requested_emp_id`, `requested_emp_name`, `from_date`, `to_date`, `submission_date`, `status`, `response_description`) VALUES
(21, 1, 'Vagga Sushruta', 'sushruta@gmail.com', 2, 'Aditya Kamath', '2025-01-20', '2025-01-20', '2025-01-13', 'Accepted', 'ok');

-- --------------------------------------------------------

--
-- Table structure for table `tbldepartments`
--

CREATE TABLE `tbldepartments` (
  `id` int(11) NOT NULL,
  `DepartmentName` varchar(150) DEFAULT NULL,
  `DepartmentShortName` varchar(100) DEFAULT NULL,
  `DepartmentCode` varchar(50) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbldepartments`
--

INSERT INTO `tbldepartments` (`id`, `DepartmentName`, `DepartmentShortName`, `DepartmentCode`, `CreationDate`) VALUES
(1, 'Computer Science and Engineering', 'CSE', 'CSE01', '2023-09-01 14:50:20'),
(2, 'Artificial Intelligence and Machine Learning', 'AIML', 'AIML01', '2023-09-01 14:50:20'),
(3, 'Information Science and Engineering', 'ISE', 'ISE01', '2023-09-01 14:50:20'),
(4, 'Other', 'Other', 'OTH01', '2023-09-01 14:50:20');

-- --------------------------------------------------------

--
-- Table structure for table `tblemployees`
--

CREATE TABLE `tblemployees` (
  `id` int(11) NOT NULL,
  `EmpId` varchar(100) NOT NULL,
  `FirstName` varchar(150) DEFAULT NULL,
  `LastName` varchar(150) DEFAULT NULL,
  `EmailId` varchar(200) DEFAULT NULL,
  `Password` varchar(180) DEFAULT NULL,
  `Gender` varchar(100) DEFAULT NULL,
  `Dob` varchar(100) DEFAULT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(200) DEFAULT NULL,
  `Country` varchar(150) DEFAULT NULL,
  `Phonenumber` char(11) DEFAULT NULL,
  `Status` int(1) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`id`, `EmpId`, `FirstName`, `LastName`, `EmailId`, `Password`, `Gender`, `Dob`, `Department`, `Address`, `City`, `Country`, `Phonenumber`, `Status`, `RegDate`) VALUES
(1, '209', 'Vagga', 'Sushruta', 'sushruta@gmail.com', 'bef8f3a1cc89bad0a47493fdf0defe8e', 'Male', '3 August, 1995', 'CSE', 'A 123 XYZ Apartment ', 'New Delhi', 'India', '7654890128', 1, '2024-09-10 14:56:23'),
(2, '011', 'Aditya', 'Kamath', 'aditya@gmail.com', '057829fa5a65fc1ace408f490be486ac', 'Male', '2 January, 1997', 'CSE', 'Hno 123 ABC Colony', 'New Delhi', 'India', '7485963210', 1, '2024-09-10 14:56:23'),
(3, '009', 'Aditi', 'S', 'aditi@gmail.com', '2b197829d548512d1d4b8bd5c773d6c3', 'Female', '9 January, 2025', 'CSE', 'bmsit', 'Other', 'India', '1234567890', 1, '2025-01-02 05:49:08');

-- --------------------------------------------------------

--
-- Table structure for table `tblleaves`
--

CREATE TABLE `tblleaves` (
  `id` int(11) NOT NULL,
  `LeaveType` varchar(110) DEFAULT NULL,
  `ToDate` date DEFAULT NULL,
  `FromDate` date DEFAULT NULL,
  `Description` mediumtext DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp(),
  `HodRemark` mediumtext DEFAULT NULL,
  `HodRemarkDate` varchar(120) DEFAULT NULL,
  `PrincipalRemark` mediumtext DEFAULT NULL,
  `PrincipalRemarkDate` varchar(120) DEFAULT NULL,
  `Status` int(1) DEFAULT NULL,
  `IsRead` int(1) DEFAULT NULL,
  `empid` int(11) DEFAULT NULL,
  `proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblleaves`
--

INSERT INTO `tblleaves` (`id`, `LeaveType`, `ToDate`, `FromDate`, `Description`, `PostingDate`, `HodRemark`, `HodRemarkDate`, `PrincipalRemark`, `PrincipalRemarkDate`, `Status`, `IsRead`, `empid`, `proof`) VALUES
(51, 'Sick Leaves', '2025-02-03', '2025-02-02', 'fever', '2025-01-13 17:34:39', 'ok', '2025-01-13 23:06:59 ', NULL, NULL, 1, 1, 1, ''),
(52, 'Official Duty(OD)', '2025-02-11', '2025-02-10', 'official duty', '2025-01-13 17:44:27', NULL, NULL, 'not uploaded correct proof', '2025-01-13 23:15:30 ', 2, 1, 1, 'assets/uploads/web1.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `tblleavetype`
--

CREATE TABLE `tblleavetype` (
  `id` int(11) NOT NULL,
  `LeaveType` varchar(200) DEFAULT NULL,
  `Description` mediumtext DEFAULT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblleavetype`
--

INSERT INTO `tblleavetype` (`id`, `LeaveType`, `Description`, `CreationDate`) VALUES
(1, 'Casual Leaves', 'Casual Leaves', '2024-12-26 14:52:22'),
(2, 'Earned Leaves', 'Earned Leaves', '2024-12-26 14:52:22'),
(3, 'Sick Leaves', 'Sick Leaves', '2024-12-26 14:52:22'),
(4, 'Restricted Leaves(RH)', 'Restricted Leaves', '2024-12-26 14:52:22'),
(5, 'Official Duty(OD)', 'Official Duty', '2024-12-26 09:22:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hod`
--
ALTER TABLE `hod`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `principal`
--
ALTER TABLE `principal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblclassrequests`
--
ALTER TABLE `tblclassrequests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbldepartments`
--
ALTER TABLE `tbldepartments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblemployees`
--
ALTER TABLE `tblemployees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblleaves`
--
ALTER TABLE `tblleaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserEmail` (`empid`);

--
-- Indexes for table `tblleavetype`
--
ALTER TABLE `tblleavetype`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hod`
--
ALTER TABLE `hod`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `principal`
--
ALTER TABLE `principal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblclassrequests`
--
ALTER TABLE `tblclassrequests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbldepartments`
--
ALTER TABLE `tbldepartments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblemployees`
--
ALTER TABLE `tblemployees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tblleaves`
--
ALTER TABLE `tblleaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `tblleavetype`
--
ALTER TABLE `tblleavetype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
