-- Create the database
CREATE DATABASE IF NOT EXISTS universitysite;
USE universitysite;

CREATE TABLE Universities (
    UniversityID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Location VARCHAR(255) NOT NULL,
    Description TEXT,
    NumberOfStudents INT,
	AdminID INT,
    ImageURL VARCHAR(255)
);
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    FullName VARCHAR(255) NOT NULL,
    UniversityID INT,
    FOREIGN KEY (UniversityID) REFERENCES Universities(UniversityID)
);

ALTER TABLE Universities
ADD FOREIGN KEY (AdminID) REFERENCES Users(UserID);

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

INSERT INTO `users` (`UserID`, `Email`, `Password`, `FullName`, `UniversityID`) VALUES
(1, 'John.A@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'John Adams', 1),
(2, 'Sarah.B@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Sarah Brown', 1),
(3, 'Michael.C@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Michael Clark', 1),
(4, 'Emily.D@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Emily Davis', 1),
(5, 'Daniel.E@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Daniel Evans', 1),
(6, 'Olivia.F@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Olivia Fernandez', 1),
(7, 'William.G@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'William Green', 1),
(8, 'Sophia.H@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Sophia Hernandez', 1),
(9, 'Christopher.I@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Christopher Irwin', 1),
(10, 'Ava.J@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Ava Jones', 1),
(11, 'Jammy@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Jamal Wallace', 1);

INSERT INTO `rsos` (`RSOID`, `Name`, `AdminID`, `UniversityID`) VALUES
(1, 'Johns Best Friends', 1, 1),
(2, 'John A Hate club', 6, 1),
(3, 'Jammy & Friends!', 11, 1);

INSERT INTO `locations` (`LocationID`, `Name`, `Latitude`, `Longitude`) VALUES
(1, 'Johns Best Friend Party!', '28.598262', '-81.209210'),
(2, 'Johns Birthday Sunday-Funday Brunch.', '28.543671', '-81.379319'),
(3, 'John A birthday ruining planning.', '28.597697', '-81.199409'),
(4, 'Jammy\'s Office Pop', '28.597039', '-81.222055');


INSERT INTO `events` (`EventID`, `Name`, `Category`, `Description`, `Time`, `Date`, `LocationID`, `ContactPhone`, `ContactEmail`, `EventType`, `RSOID`, `UniversityID`, `Approved`) VALUES
(1, 'Johns Best Friend Party!', 'Birthday party.', 'Johns dorm party! Be there if you are a real friend!', '18:00:00', '2023-06-12', 1, '801-343-56', 'John.A@knights.ucf.edu', 'rso', 1, 1, 0),
(2, 'Johns Birthday Sunday-Funday Brunch.', 'Brunch', 'Brunch downtown at elixre with my best friends!', '10:00:00', '2023-06-11', 2, '801-343-56', 'John.A@knights.ucf.edu', 'rso', 1, 1, 0),
(3, 'John A birthday ruining planning.', 'Being Mean', 'We are meeting up to discuss how to ruin his birthday!', '15:00:00', '2023-06-11', 3, '3284449827', 'Olivia.F@knights.ucf.edu', 'rso', 2, 1, 0),
(4, 'Jammy\'s Office Pop', 'Party', 'Jammy office pop party! Everyone come!', '18:00:00', '2023-04-28', 4, '865-818-90', 'jammy@knights.ucf.edu', 'rso', 3, 1, 0);



INSERT INTO `ratings` (`UserID`, `EventID`, `Stars`) VALUES
(1, 1, 5),
(1, 2, 4),
(6, 3, 5);

INSERT INTO `comments` (`CommentID`, `UserID`, `EventID`, `Content`, `Timestamp`) VALUES
(1, 6, 3, 'Haha he sucks!', '2023-04-18 00:04:08'),
(2, 1, 1, 'So excited guys!', '2023-04-18 00:04:33'),
(3, 1, 2, 'Its gonna be a blast everyone!', '2023-04-18 00:04:51');

INSERT INTO `rsomembers` (`UserID`, `RSOID`) VALUES
(1, 1),
(2, 1),
(3, 1),
(3, 3),
(4, 1),
(4, 2),
(5, 1),
(5, 3),
(6, 2),
(7, 2),
(8, 2),
(8, 3),
(9, 2),
(10, 2),
(10, 3),
(11, 3);
