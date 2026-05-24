-- ============================================================
-- Hospital Management System (HMS) — Database Setup
-- Project: FINAL-BOSS-LEGIT
-- Rebuilt from: kishan0725/Hospital-Management-System
-- Stack: HTML + Bootstrap 5 + PHP + MySQL
-- Author: kiro-kirochan
-- ============================================================

CREATE DATABASE IF NOT EXISTS `hospital`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `hospital`;

-- ============================================================
-- TABLE 1: specialtb (Doctor Specializations)
-- Stores available doctor specialties used by the doctor form.
-- ============================================================
DROP TABLE IF EXISTS `specialtb`;
CREATE TABLE `specialtb` (
    `id`              int(11)      NOT NULL AUTO_INCREMENT,
    `specialization`  varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `specialtb` (`specialization`) VALUES
('Cardiology'),
('Pediatrics'),
('Dermatology'),
('Neurology'),
('General Medicine');

-- ============================================================
-- TABLE 2: doctb (Doctors)
-- Stores doctor records managed in the Doctors module.
-- Primary Key: id (auto-increment integer)
-- ============================================================
DROP TABLE IF EXISTS `doctb`;
CREATE TABLE `doctb` (
    `id`            int(11)      NOT NULL AUTO_INCREMENT,
    `name`          varchar(100) NOT NULL,
    `email`         varchar(100) NOT NULL,
    `password`      varchar(255) NOT NULL,
    `specialization` varchar(100) NOT NULL,
    `docFees`       decimal(10,2) NOT NULL DEFAULT '0.00',
    `created_at`    datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample doctor records
INSERT INTO `doctb` (`name`, `email`, `password`, `specialization`, `docFees`) VALUES
('Dr. John Smith', 'john.smith@hospital.com', '$2y$10$r9uZFUGdUFzHMZciwLnwxO/b3eSrP26oqldA31ExqOXCCA1UghPC6', 'Cardiology', 150.00),
('Dr. Sarah Jenkins', 'sarah.jenkins@hospital.com', '$2y$10$r9uZFUGdUFzHMZciwLnwxO/b3eSrP26oqldA31ExqOXCCA1UghPC6', 'Pediatrics', 140.00),
('Dr. Michael Chang', 'michael.chang@hospital.com', '$2y$10$r9uZFUGdUFzHMZciwLnwxO/b3eSrP26oqldA31ExqOXCCA1UghPC6', 'Dermatology', 130.00),
('Dr. Emily Watson', 'emily.watson@hospital.com', '$2y$10$r9uZFUGdUFzHMZciwLnwxO/b3eSrP26oqldA31ExqOXCCA1UghPC6', 'Neurology', 160.00),
('Dr. Robert Patel', 'robert.patel@hospital.com', '$2y$10$r9uZFUGdUFzHMZciwLnwxO/b3eSrP26oqldA31ExqOXCCA1UghPC6', 'General Medicine', 120.00);

-- ============================================================
-- TABLE 3: patreg (Patients)
-- Stores patient records managed in the Patients module.
-- Primary Key: id (auto-increment integer)
-- ============================================================
DROP TABLE IF EXISTS `patreg`;
CREATE TABLE `patreg` (
    `id`       int(11)      NOT NULL AUTO_INCREMENT,
    `fname`    varchar(50)  NOT NULL,
    `lname`    varchar(50)  NOT NULL,
    `email`    varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `contact`  varchar(15)  NOT NULL,
    `gender`   enum('Male','Female','Other') NOT NULL DEFAULT 'Other',
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample patient records
INSERT INTO `patreg` (`fname`, `lname`, `email`, `password`, `contact`, `gender`) VALUES
('Alice', 'Reyes', 'alice.reyes@email.com', '$2y$10$r9uZFUGdUFzHMZciwLnwxO/b3eSrP26oqldA31ExqOXCCA1UghPC6', '09171234567', 'Female'),
('Ben', 'Torres', 'ben.torres@email.com', '$2y$10$r9uZFUGdUFzHMZciwLnwxO/b3eSrP26oqldA31ExqOXCCA1UghPC6', '09281234567', 'Male'),
('Clara', 'Santos', 'clara.santos@email.com', '$2y$10$r9uZFUGdUFzHMZciwLnwxO/b3eSrP26oqldA31ExqOXCCA1UghPC6', '09391234567', 'Female');

-- ============================================================
-- TABLE 4: appointmenttb (Appointments)
-- Links patients and doctors via foreign keys.
-- Primary Key: id
-- Foreign Keys: pid → patreg(id), did → doctb(id)
-- ============================================================
DROP TABLE IF EXISTS `appointmenttb`;
CREATE TABLE `appointmenttb` (
    `id`     int(11) NOT NULL AUTO_INCREMENT,
    `pid`    int(11) NOT NULL,
    `did`    int(11) NOT NULL,
    `apdate` date    NOT NULL,
    `aptime` time    NOT NULL,
    `reason` text    DEFAULT NULL,
    `status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`pid`) REFERENCES `patreg`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`did`) REFERENCES `doctb`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample appointment records
INSERT INTO `appointmenttb` (`pid`, `did`, `apdate`, `aptime`, `reason`, `status`) VALUES
(1, 1, '2026-06-01', '09:00:00', 'Chest pain and shortness of breath', 'Confirmed'),
(2, 3, '2026-06-02', '10:30:00', 'Skin rash on arms', 'Pending'),
(3, 2, '2026-06-03', '14:00:00', 'Fever and cough for 3 days', 'Pending');
