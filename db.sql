-- Create the database
CREATE DATABASE IF NOT EXISTS universitysite;
USE universitysite;

CREATE TABLE Universities (
    UniversityID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Location VARCHAR(255) NOT NULL,
    Description TEXT,
    NumberOfStudents INT,
    ImageURL VARCHAR(255)
);
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    FullName VARCHAR(255) NOT NULL,
    UserType ENUM('super_admin', 'admin', 'student') DEFAULT 'student',
    UniversityID INT,
    FOREIGN KEY (UniversityID) REFERENCES Universities(UniversityID)
);
CREATE TABLE RSOs (
    RSOID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    AdminID INT,
    UniversityID INT,
    FOREIGN KEY (AdminID) REFERENCES Users(UserID),
    FOREIGN KEY (UniversityID) REFERENCES Universities(UniversityID)
);
CREATE TABLE RSOMembers (
    UserID INT,
    RSOID INT,
    PRIMARY KEY (UserID, RSOID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (RSOID) REFERENCES RSOs(RSOID)
);
CREATE TABLE Locations (
    LocationID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Latitude DECIMAL(9,6) NOT NULL,
    Longitude DECIMAL(9,6) NOT NULL
);
CREATE TABLE Events (
    EventID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Category VARCHAR(255) NOT NULL,
    Description TEXT,
    Time TIME NOT NULL,
    Date DATE NOT NULL,
    LocationID INT,
    ContactPhone VARCHAR(20),
    ContactEmail VARCHAR(255),
    EventType ENUM('public', 'private', 'rso') NOT NULL,
    RSOID INT,
    UniversityID INT,
    Approved BOOLEAN,
    FOREIGN KEY (LocationID) REFERENCES Locations(LocationID),
    FOREIGN KEY (RSOID) REFERENCES RSOs(RSOID),
    FOREIGN KEY (UniversityID) REFERENCES Universities(UniversityID)
);
CREATE TABLE Comments (
    CommentID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    EventID INT,
    Content TEXT NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (EventID) REFERENCES Events(EventID)
);
CREATE TABLE Ratings (
    UserID INT,
    EventID INT,
    Stars INT NOT NULL,
    PRIMARY KEY (UserID, EventID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (EventID) REFERENCES Events(EventID)
);



INSERT INTO `universities` (`UniversityID`, `Name`, `Location`, `Description`, `NumberOfStudents`, `ImageURL`) VALUES
(1, 'University of Central Florida', 'Orlando, Florida', 'UCF Description!', 50000, 'https://1000logos.net/wp-content/uploads/2017/11/University-of-Central-Florida-Logo.png');