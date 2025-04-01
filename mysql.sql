-- Create the database
CREATE DATABASE IF NOT EXISTS BowserReports;
USE BowserReports;

-- Users table (Primary table)
CREATE TABLE Users (
    userId INT(11) PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Should be hashed in practice
    sessionKey VARCHAR(255),
    active TINYINT(1) DEFAULT 1,
    userType TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bowser table
CREATE TABLE Bowser (
    bowserId INT(11) PRIMARY KEY AUTO_INCREMENT,
    ownerId INT(11) NOT NULL,
    manufacturer_details TEXT,
    serial_number VARCHAR(100),
    specific_notes TEXT,
    capacity_litres DECIMAL(10,2),
    length_num DECIMAL(10,2),
    width_num DECIMAL(10,2),
    weight_empty_kg DECIMAL(10,2),
    weight_full_kg DECIMAL(10,2),
    supplier_company VARCHAR(255),
    date_received DATE,
    date_returned DATE,
    postcode VARCHAR(20),
    longitude DECIMAL(10,6),
    latitude DECIMAL(10,6),
    northings INT(11),
    eastings INT(11),
    availability TINYINT(1) DEFAULT 1,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ownerId) REFERENCES Users(userId)
) ENGINE=InnoDB;

-- Reports table
CREATE TABLE Reports (
    reportId INT(11) PRIMARY KEY AUTO_INCREMENT,
    userId INT(11) NOT NULL,
    bowserId INT(11) NOT NULL,
    report TEXT NOT NULL,
    typeOfReport TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES Users(userId),
    FOREIGN KEY (bowserId) REFERENCES Bowser(bowserId)
) ENGINE=InnoDB;

-- Uploads table
CREATE TABLE Uploads (
    uploadId INT(11) PRIMARY KEY AUTO_INCREMENT,
    fileName TEXT NOT NULL,
    bowserId INT(11) NOT NULL,
    uploadedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bowserId) REFERENCES Bowser(bowserId)
) ENGINE=InnoDB;

-- MaintainBowser table
CREATE TABLE MaintainBowser (
    maintainbowserId INT(11) PRIMARY KEY AUTO_INCREMENT,
    bowserId INT(11) NOT NULL,
    userId INT(11) NOT NULL,
    description_of_work TEXT NOT NULL,
    maintenance_type TEXT,
    date_of_maintenance DATE,
    status TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bowserId) REFERENCES Bowser(bowserId),
    FOREIGN KEY (userId) REFERENCES Users(userId)
) ENGINE=InnoDB;

-- AreaReports table
CREATE TABLE AreaReports (
    areaReportId INT(11) PRIMARY KEY AUTO_INCREMENT,
    report TEXT NOT NULL,
    postcode VARCHAR(20),
    report_type TEXT,
    userId INT(11) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES Users(userId)
) ENGINE=InnoDB;

-- ActiveBowser table
CREATE TABLE ActiveBowser (
    activebowserId INT(11) PRIMARY KEY AUTO_INCREMENT,
    bowserId INT(11) NOT NULL,
    userId INT(11) NOT NULL,
    dispatch_date DATE,
    dispatch_type TEXT,
    status TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bowserId) REFERENCES Bowser(bowserId),
    FOREIGN KEY (userId) REFERENCES Users(userId)
) ENGINE=InnoDB;