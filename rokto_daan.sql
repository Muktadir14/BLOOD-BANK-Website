-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 27, 2023 at 05:11 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rokto_daan`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `name` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`name`, `email`, `password`) VALUES
('Fahmida Faiza', 'f.faizaaa05@gmail.com', '$2y$10$3dcDFZTLr8Y2TchLWpccKO2w0KXNpcaxkmuu691JVEd/wIseo.Fbe'),
('Sariya Islam', 'sariya12@gmail.com', '$2y$10$oVCE2iQ1Ii5XpoSSeaZeZu8xxOaNTX4T/C/uG..SnAvYKQlpQ/iH2'),
('Salna Halim', 'salna@gmail.com', '$2y$10$i.PLu.J4w5LmipQbDF6M0eu2oFgZvCYIJsOjhRr/BC7GQgJcdzBj2');

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

CREATE TABLE `availability` (
  `bloodTypes` varchar(255) NOT NULL,
  `remainingUnits` int(11) DEFAULT NULL,
  `bloodbankID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`bloodTypes`, `remainingUnits`, `bloodbankID`) VALUES
('A+', 10, 1),
('A+', 10, 5),
('B+', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bloodbank`
--

CREATE TABLE `bloodbank` (
  `bloodbankID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bloodbank`
--

INSERT INTO `bloodbank` (`bloodbankID`, `name`, `phone`, `region`, `address`, `email`, `password`) VALUES
(1, 'Quantum Foundation', '+88 01617 826886', 'Gulshan', '1076, Eidgah Moshjid Road, Shahjadpur, Gulshan, Dhaka 1212', 'webmaster@quantummethod.org.bd', '$2y$10$UYDF7fsoywWWRNDwiDMceeVVUq7NywvwgyGp8jtYR5ytRz91G1X3G'),
(2, 'NSU Blood donation club', '7117243', 'Vatara', 'nsu campus', 'nsubd@gmail.com', '$2y$10$zFC2hhVLOPQAi6xEL7miuu3gckRXeZ7yib7qWQPGt70HXvXi6fRUG'),
(3, 'shondhani', '123342', 'Abdullahpur', 'abdullahpur bazar', 'shondha@gmail.com', '$2y$10$R3J6HCmd8l63DYcOtmKX.uyf9HiCS3vIhH2lmON/EnvmvQ5U4rXR6'),
(4, 'Special Blood Bank', '01711402563', 'Demra', 'Ideal college', 'sbb@gmail.com', '$2y$10$YzqiXx4dRSBl4/YO9Ja6sulOoaoGWGpjsQpOYyjsOWbepXGeGJnqq'),
(5, 'Red Crecent Blood Centre', ' 01811-458537', 'Mohammadpur', 'Red Crecent Blood Centre, 7, 5 Aurangajeb Rd, Dhaka', 'info@bdrcs.org', '$2y$10$Ue5qz6UX0N.VVFSbaMzlLexACUyAJaZtLcCvLy.wB/pOg1Rw3C2wC');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `donationRecordID` int(11) NOT NULL,
  `did` varchar(255) DEFAULT NULL,
  `numberofDonations` int(11) DEFAULT NULL,
  `donationDate` date DEFAULT NULL,
  `eventID` int(11) DEFAULT NULL,
  `bloodbankID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`donationRecordID`, `did`, `numberofDonations`, `donationDate`, `eventID`, `bloodbankID`) VALUES
(1, '1961394580', 1, '2023-09-20', 1, NULL),
(2, '1961394580', 2, '2023-09-21', NULL, 3);

--
-- Triggers `donors`
--
DELIMITER $$
CREATE TRIGGER `increment_participants_count` AFTER INSERT ON `donors` FOR EACH ROW BEGIN
    UPDATE participants
    SET numberofParticipants = numberofParticipants + 1
    WHERE eventID = NEW.eventID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `eventdate` date NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `name`, `eventdate`, `region`, `location`, `contact`, `email`, `password`) VALUES
(1, 'NSU BLOOD DONATION DRIVE', '2023-09-30', 'Vatara', 'NSU Campus.', '727840', 'nsu@gmail.com', '$2y$10$Fyl9lcYOCrSDiTeqysV0Bu5FzFaW/GN3WJoDMOu6s440zlzeptb5W'),
(2, 'NSU BLOOD DONATION DRIVE', '2023-10-19', 'Khilkhet', 'Nikunja', '3445161', 'nikunjansu@gmail.com', '$2y$10$IkH3wiyoPq/pXfa5fyzNqeSdig2MgtU14DSXPkCkYosIj1T0iwO/G'),
(3, 'Shondhani', '2023-11-26', 'Mohammadpur', 'Aurangajeb Road', '01789243456', 'shondhanidrive@gmail.com', 'abc123'),
(4, 'Shondhani', '2023-09-26', 'Mohammadpur', 'Aurangajeb Road', '01789243456', 'shondhanidrive@gmail.com', 'abc123'),
(5, 'Shondhani', '2023-10-03', 'Mohammadpur', 'Aurangajeb Road', '01789243456', 'shondhanidrive@gmail.com', 'abc123'),
(6, 'Donate_to_save', '2023-11-15', 'Mohakhali', 'Brac campus', '0171234456', 'bracsocial service@gmail.com', 'abc123'),
(7, 'Rokto dow', '2023-08-15', 'Dhanmondi', 'Road-27', '0171888456', 'rokto_dow@gmail.com', 'abc123');

--
-- Triggers `events`
--
DELIMITER $$
CREATE TRIGGER `add_event_to_participants` AFTER INSERT ON `events` FOR EACH ROW BEGIN
    INSERT INTO participants (eventdate, eventID, name)
    VALUES (NEW.eventdate, NEW.eventID, NEW.name);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `eventdate` date DEFAULT NULL,
  `eventID` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `numberofParticipants` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`eventdate`, `eventID`, `name`, `numberofParticipants`) VALUES
('2023-10-19', 2, 'NSU BLOOD DONATION DRIVE', 2);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patientRecordID` int(11) NOT NULL,
  `pid` varchar(255) DEFAULT NULL,
  `bloodRequestType` varchar(255) DEFAULT NULL,
  `numberofUnits` int(11) DEFAULT NULL,
  `requestDate` date DEFAULT NULL,
  `bloodbankID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patientRecordID`, `pid`, `bloodRequestType`, `numberofUnits`, `requestDate`, `bloodbankID`) VALUES
(1, '1033032796', 'O+', 5, '2023-09-20', 3),
(2, '1033032796', 'A-', 7, '2023-09-30', 2),
(3, '1961394580', 'O+', 5, '2023-09-20', 1),
(6, '1961394580', 'B+', 1, '2023-09-30', 1),
(8, '1961394580', 'A+', 1, '2023-09-26', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `nid` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `disease` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`nid`, `first_name`, `last_name`, `email`, `password`, `gender`, `blood_group`, `disease`, `birthdate`, `phone_number`, `address`) VALUES
('1033032796', 'Fahmida', 'Faiza', 'f.faizaaa05@gmail.com', '$2y$10$e83tM0cF.XcXP5OZEBaiYugLx./BAljFrd0Q4VcP9DM.Zl6eQX.Ie', 'female', 'O+', 'Sensitive', '2001-08-05', '01707555797', 'Bashundhara R/A.'),
('12345566777', 'Fariha', 'Haque', 'fh@gmail.com', '$2y$10$pcQu9Pn3sQf/JUMq62e94uRXhYg./GLbjVzot6IIRAGPeX94P3mbO', 'female', 'B-', 'None', '2023-09-14', '019223465', 'Bashundhara R/A.'),
('1961394580', 'Muktadir', 'Hassan', 'adib.ponting@gmail.com', '$2y$10$TTtsaaFqlhRJatWy1tb87Oj7bWc2XjatCF.FxjOxPGPq710mEu7dW', 'male', 'A+', 'None', '2001-11-22', '01790891278', 'Mohammadpur'),
('2031418642', 'Fariha', 'Islam', 'fi@gmail.com', '$2y$10$CuANqO0eQSUEKYhq2YiSE.bemY1.ECV5C/3AcF2Y9CQ5AFyLGyW/K', 'female', 'A+', 'None', '2009-06-09', '01711402563', 'Bashundhara R/A'),
('2031419642', 'Salma', 'Halim', 'salma12@gmail.com', '$2y$10$U2XCmSZo08wUoHRF3a2zQu..ic4zUmzIugJXCQcg8mRh/k7jhOVM.', 'female', 'A+', 'Diabetes', '1993-11-08', '01798253711', 'Malibagh ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`bloodTypes`,`bloodbankID`),
  ADD KEY `idx_bloodbankID` (`bloodbankID`);

--
-- Indexes for table `bloodbank`
--
ALTER TABLE `bloodbank`
  ADD PRIMARY KEY (`bloodbankID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`donationRecordID`),
  ADD KEY `idx_bloodbankID` (`bloodbankID`),
  ADD KEY `idx_eventID` (`eventID`),
  ADD KEY `donors_ibfk_1` (`did`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD KEY `eventID` (`eventID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patientRecordID`),
  ADD KEY `pid` (`pid`),
  ADD KEY `bloodbankID` (`bloodbankID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`nid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bloodbank`
--
ALTER TABLE `bloodbank`
  MODIFY `bloodbankID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `donationRecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patientRecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `availability`
--
ALTER TABLE `availability`
  ADD CONSTRAINT `bloodbank_index_ID` FOREIGN KEY (`bloodbankID`) REFERENCES `bloodbank` (`bloodbankID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `donors`
--
ALTER TABLE `donors`
  ADD CONSTRAINT `bloodbankCon` FOREIGN KEY (`bloodbankID`) REFERENCES `bloodbank` (`bloodbankID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `donors_ibfk_1` FOREIGN KEY (`did`) REFERENCES `users` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eventsCon` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`);

--
-- Constraints for table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `bloodbankID_index` FOREIGN KEY (`bloodbankID`) REFERENCES `bloodbank` (`bloodbankID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `users` (`nid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
