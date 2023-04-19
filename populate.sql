-- Dummy data SQL file
-- Create the database
CREATE DATABASE IF NOT EXISTS universitysite;
USE universitysite;

CREATE TABLE Universities
(
    UniversityID     INT AUTO_INCREMENT PRIMARY KEY,
    Name             VARCHAR(255) NOT NULL,
    Location         VARCHAR(255) NOT NULL,
    Description      TEXT,
    NumberOfStudents INT,
    AdminID          INT,
    ImageURL         VARCHAR(255)
);
CREATE TABLE Users
(
    UserID       INT AUTO_INCREMENT PRIMARY KEY,
    Email        VARCHAR(255) NOT NULL UNIQUE,
    Password     VARCHAR(255) NOT NULL,
    FullName     VARCHAR(255) NOT NULL,
    UniversityID INT,
    FOREIGN KEY (UniversityID) REFERENCES Universities (UniversityID)
);

ALTER TABLE Universities
    ADD FOREIGN KEY (AdminID) REFERENCES Users (UserID);

CREATE TABLE RSOs
(
    RSOID        INT AUTO_INCREMENT PRIMARY KEY,
    Name         VARCHAR(255) NOT NULL,
    Description  VARCHAR(512) NOT NULL,
    AdminID      INT,
    UniversityID INT,
    ImageURL     VARCHAR(512),
    Active       BOOLEAN DEFAULT true,
    FOREIGN KEY (AdminID) REFERENCES Users (UserID),
    FOREIGN KEY (UniversityID) REFERENCES Universities (UniversityID)
);
CREATE TABLE RSOMembers
(
    UserID INT,
    RSOID  INT,
    PRIMARY KEY (UserID, RSOID),
    FOREIGN KEY (UserID) REFERENCES Users (UserID),
    FOREIGN KEY (RSOID) REFERENCES RSOs (RSOID)
);
CREATE TABLE Locations
(
    LocationID INT AUTO_INCREMENT PRIMARY KEY,
    Name       VARCHAR(255)  NOT NULL,
    Latitude   DECIMAL(9, 6) NOT NULL,
    Longitude  DECIMAL(9, 6) NOT NULL
);
CREATE TABLE Events
(
    EventID      INT AUTO_INCREMENT PRIMARY KEY,
    Name         VARCHAR(255)                      NOT NULL,
    Category     VARCHAR(255)                      NOT NULL,
    Description  TEXT,
    Time         TIME                              NOT NULL,
    Date         DATE                              NOT NULL,
    LocationID   INT,
    ContactPhone VARCHAR(20),
    ContactEmail VARCHAR(255),
    EventType    ENUM ('public', 'private', 'rso') NOT NULL,
    RSOID        INT,
    UniversityID INT,
    Approved     BOOLEAN,
    FOREIGN KEY (LocationID) REFERENCES Locations (LocationID),
    FOREIGN KEY (RSOID) REFERENCES RSOs (RSOID),
    FOREIGN KEY (UniversityID) REFERENCES Universities (UniversityID)
);
CREATE TABLE Comments
(
    CommentID INT AUTO_INCREMENT PRIMARY KEY,
    UserID    INT,
    EventID   INT,
    Content   TEXT NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users (UserID),
    FOREIGN KEY (EventID) REFERENCES Events (EventID)
);
CREATE TABLE Ratings
(
    UserID  INT,
    EventID INT,
    Stars   INT NOT NULL,
    PRIMARY KEY (UserID, EventID),
    FOREIGN KEY (UserID) REFERENCES Users (UserID),
    FOREIGN KEY (EventID) REFERENCES Events (EventID)
);


ALTER TABLE Ratings
    ADD CONSTRAINT check_stars_range CHECK (Stars BETWEEN 1 AND 5);

ALTER TABLE Universities
    ADD CONSTRAINT unique_admin UNIQUE (AdminID);

DELIMITER $$
CREATE TRIGGER RsoActivityStatusAdd
    AFTER INSERT
    ON RSOMEMBERS
    FOR EACH ROW
BEGIN
    IF ((SELECT COUNT(*) FROM RSOMEMBERS M WHERE M.RSOID = NEW.RSOID) > 4)
    THEN
        UPDATE RSOs
        SET Active = true
        WHERE RSOID = NEW.RSOID;
    END IF;
END $$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER RsoActivityStatusRemove
    AFTER DELETE
    ON RSOMEMBERS
    FOR EACH ROW
BEGIN
    IF ((SELECT COUNT(*) FROM RSOMEMBERS M WHERE M.RSOID = OLD.RSOID) < 5)
    THEN
        UPDATE RSOs
        SET Active = false
        WHERE RSOID = OLD.RSOID;
    END IF;
END $$
DELIMITER ;

-- Reset the delimiter back to ;
DELIMITER ;

-- Universities
INSERT INTO `Universities` (`UniversityID`, `Name`, `Location`, `Description`, `NumberOfStudents`, `ImageURL`)
VALUES (1, 'University of Central Florida', 'Orlando, Florida',
        'UCF is an emerging preeminent research university in Florida & one of the best colleges for quality, access, impact & value.',
        69000, 'https://1000logos.net/wp-content/uploads/2017/11/University-of-Central-Florida-Logo.png'),
       (2, 'Florida State University', 'Tallahassee, Florida',
        'Florida State University is a public research university with an acceptance rate of 36%.', 42000,
        'https://1000logos.net/wp-content/uploads/2017/11/Florida-State-University.png'),
       (3, 'University of Florida', 'Gainesville, Florida',
        'The University of Florida is a top public research university in the suburban center of vibrant Gainesville, Florida.',
        52000, 'https://1000logos.net/wp-content/uploads/2017/11/University-of-Florida-Logo.png');

-- Users
INSERT INTO `Users` (`UserID`, `Email`, `Password`, `FullName`, `UniversityID`)
VALUES (1, 'John.A@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'John Adams', 1),
       (2, 'Sarah.B@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Sarah Brown', 1),
       (3, 'Michael.C@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Michael Clark',
        1),
       (4, 'Emily.D@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Emily Davis', 1),
       (5, 'Daniel.E@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Daniel Evans',
        1),
       (6, 'Olivia.F@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC',
        'Olivia Fernandez', 1),
       (7, 'William.G@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'William Green',
        1),
       (8, 'Sophia.H@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC',
        'Sophia Hernandez', 1),
       (9, 'Christopher.I@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC',
        'Christopher Irwin', 1),
       (10, 'Ava.J@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Ava Jones', 1),
       (11, 'Jammy@knights.ucf.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Jamal Wallace',
        1),
       (12, 'Megan.A@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Megan Adams', 2),
       (13, 'Steve.B@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Steve Brown', 2),
       (14, 'Michelle.C@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Michelle Clark', 2),
       (15, 'Ethan.D@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Ethan Davis', 2),
       (16, 'Danielle.E@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Danielle Evans', 2),
       (17, 'Oliver.F@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Oliver Fernandez', 2),
       (18, 'Wendy.G@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Wendy Green', 2),
       (19, 'Samantha.H@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Samantha Hernandez',
        2),
       (20, 'Chris.J@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Chris Johnson', 2),
       (21, 'James.K@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'James King', 2),
       (22, 'Maggie.L@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Maggie Lee', 2),
       (23, 'Stacy.M@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Stacy Martin', 2),
       (24, 'Mitch.N@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Mitch Nelson', 2),
       (25, 'Ellie.O@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Ellie Oliver', 2),
       (26, 'Pete.P@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Pete Parker', 2),
       (27, 'Quincy.Q@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Quincy Quinn', 2),
       (28, 'Randy.R@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Randy Reed', 2),
       (29, 'Sandy.S@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Sandy Smith', 2),
       (30, 'Tiffany.T@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Tiffany Thompson', 2),
       (31, 'Ursula.U@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Ursula Underwood', 2),
       (32, 'Victor.V@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Victor Vasquez', 2),
       (33, 'Wendy.W@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Wendy Williams', 2),
       (34, 'Xander.X@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Xander Xavier', 2),
       (35, 'Yolanda.Y@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Yolanda Young', 2),
       (36, 'Zachary.Z@fsu.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Zachary Zimmerman',
        2),
       (37, 'Aaron.A@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Aaron Adams', 3),
       (38, 'Beth.B@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Beth Brown', 3),
       (39, 'Charlie.C@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Charlie Clark', 3),
       (40, 'Diana.D@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Diana Davis', 3),
       (41, 'Eric.E@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Eric Evans', 3),
       (42, 'Felicity.F@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Felicity Fernandez',
        3),
       (43, 'George.G@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'George Green', 3),
       (44, 'Hannah.H@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Hannah Hernandez', 3),
       (45, 'Igor.I@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Igor Irwin', 3),
       (46, 'Julie.J@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Julie Jones', 3),
       (47, 'Kevin.K@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Kevin King', 3),
       (48, 'Laura.L@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Laura Lee', 3),
       (49, 'Mike.M@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Mike Martin', 3),
       (50, 'Nancy.N@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Nancy Nelson', 3),
       (51, 'Oscar.O@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Oscar Oliver', 3),
       (52, 'Pamela.P@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Pamela Parker', 3),
       (53, 'Quentin.Q@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Quentin Quinn', 3),
       (54, 'Rachel.R@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Rachel Reed', 3),
       (55, 'Steve.S@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Steve Smith', 3),
       (56, 'Tina.T@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Tina Thompson', 3),
       (57, 'Uma.U@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Uma Underwood', 3),
       (58, 'Vince.V@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Vince Vasquez', 3),
       (59, 'Wendy.W@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo66vTCByLKa9M3wFC', 'Wendy Williams', 3),
       (60, 'Xavier.X@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Xavier Xander', 3),
       (61, 'Yasmin.Y@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Yasmin Young', 3),
       (62, 'Zane.Z@ufl.edu', '$2y$10$B/WXX.o/v7qRSivz7D2tE.5QhAhGUYsA5PBreo6vTCByLKa9M3wFC', 'Zane Zimmerman', 3);


-- RSOS
INSERT INTO `rsos` (`RSOID`, `Name`, `Description`, `AdminID`, `UniversityID`, `ImageURL`)
VALUES (1, 'UCF Coding Club', 'A club for students interested in coding and programming.', 1, 1, ''),
       (2, 'UCF Robotics Society', 'A society dedicated to robotics and automation.', 2, 1, ''),
       (3, 'UCF Chess Club', 'A club for students who enjoy playing chess.', 3, 1, ''),
       (4, 'UCF Environmental Club', 'A club promoting environmental awareness and sustainability.', 4, 1, ''),
       (5, 'UCF Engineering Society', 'A society for students pursuing engineering degrees.', 5, 1, ''),
       (6, 'FSU Coding Club', 'A club for students interested in coding and programming.', 12, 2, ''),
       (7, 'FSU Robotics Society', 'A society dedicated to robotics and automation.', 13, 2, ''),
       (8, 'FSU Chess Club', 'A club for students who enjoy playing chess.', 14, 2, ''),
       (9, 'FSU Environmental Club', 'A club promoting environmental awareness and sustainability.', 15, 2, ''),
       (10, 'FSU Engineering Society', 'A society for students pursuing engineering degrees.', 16, 2, ''),
       (11, 'UF Coding Club', 'A club for students interested in coding and programming.', 37, 3, ''),
       (12, 'UF Robotics Society', 'A society dedicated to robotics and automation.', 38, 3, ''),
       (13, 'UF Chess Club', 'A club for students who enjoy playing chess.', 39, 3, ''),
       (14, 'UF Environmental Club', 'A club promoting environmental awareness and sustainability.', 40, 3, ''),
       (15, 'UF Engineering Society', 'A society for students pursuing engineering degrees.', 41, 3, '');


INSERT INTO `rsomembers` (`UserID`, `RSOID`) VALUES
(1, 1),
(2, 1),
(2, 21),
(3, 21),
(5, 1),
(5, 21),
(7, 1),
(8, 1),
(41, 15),
(42, 15),
(43, 15),
(44, 15),
(45, 15),
(67, 1),
(67, 21),
(68, 15);



INSERT INTO `locations` (`LocationID`, `Name`, `Latitude`, `Longitude`)
VALUES (1, '4000 Central Florida Blvd, Orlando, FL 32816', 28.6024, -81.2001),
       (2, '12777 Gemini Blvd N, Orlando, FL 32816', 28.5960, -81.1918),
       (3, '5000 Colbourn Hall, Orlando, FL 32816', 28.6053, -81.1970),
       (4, '4110 Libra Dr, Orlando, FL 32816', 28.6017, -81.1944),
       (5, '12800 Pegasus Dr, Orlando, FL 32816', 28.6072, -81.1962),
       (6, '104 N Woodward Ave, Tallahassee, FL 32304', 30.4429, -84.2985),
       (7, '75 N Woodward Ave, Tallahassee, FL 32304', 30.4447, -84.2990),
       (8, '644 W Call St, Tallahassee, FL 32304', 30.4422, -84.2950),
       (9, '100 S Woodward Ave, Tallahassee, FL 32304', 30.4419, -84.2909),
       (10, '110 S Woodward Ave, Tallahassee, FL 32304', 30.4408, -84.2917),
       (11, '1515 Museum Rd, Gainesville, FL 32611', 29.6474, -82.3464),
       (12, '1558 Union Rd, Gainesville, FL 32611', 29.6475, -82.3495),
       (13, '1290 Newell Dr, Gainesville, FL 32611', 29.6402, -82.3416),
       (14, '1523 Union Rd, Gainesville, FL 32611', 29.6436, -82.3478),
       (15, '1370 Inner Rd, Gainesville, FL 32611', 29.6430, -82.3420);

-- Events
INSERT INTO `events` (`EventID`, `Name`, `Category`, `Description`, `Time`, `Date`, `LocationID`, `ContactPhone`,
                      `ContactEmail`, `EventType`, `RSOID`, `UniversityID`, `Approved`)
VALUES (1, 'UCF Coding Club Hackathon', 'Technology', 'A 24-hour coding competition.', '10:00:00', '2022-11-05', 1,
        '555-1234', 'ucfcc@example.com', 'RSO', 1, 1, 1),
       (2, 'UCF Robotics Society Workshop', 'Technology', 'A workshop on building robots.', '14:00:00', '2022-11-12', 2,
        '555-2345', 'ucfrobotics@example.com', 'RSO', 2, 1, 1),
       (3, 'UCF Chess Club Tournament', 'Games', 'A friendly chess tournament.', '13:00:00', '2022-11-19', 3,
        '555-3456', 'ucfchess@example.com', 'RSO', 3, 1, 1),
       (4, 'UCF Environmental Club Cleanup', 'Environment', 'A campus cleanup event.', '09:00:00', '2022-11-26', 4,
        '555-4567', 'ucfenv@example.com', 'RSO', 4, 1, 1),
       (5, 'FSU Coding Club Hackathon', 'Technology', 'A 24-hour coding competition.', '10:00:00', '2022-11-05', 6,
        '555-5678', 'fsucc@example.com', 'RSO', 6, 2, 1),
       (6, 'FSU Robotics Society Workshop', 'Technology', 'A workshop on building robots.', '14:00:00', '2022-11-12', 7,
        '555-6789', 'fsurobotics@example.com', 'RSO', 7, 2, 1),
       (7, 'FSU Chess Club Tournament', 'Games', 'A friendly chess tournament.', '13:00:00', '2022-11-19', 8,
        '555-7890', 'fsuchess@example.com', 'RSO', 8, 2, 1),
       (8, 'FSU Environmental Club Cleanup', 'Environment', 'A campus cleanup event.', '09:00:00', '2022-11-26', 9,
        '555-8901', 'fsuenv@example.com', 'RSO', 9, 2, 1),
       (9, 'UF Coding Club Hackathon', 'Technology', 'A 24-hour coding competition.', '10:00:00', '2022-11-05', 11,
        '555-9012', 'ufcc@example.com', 'RSO', 11, 3, 1),
       (10, 'UF Robotics Society Workshop', 'Technology', 'A workshop on building robots.', '14:00:00', '2022-11-12',
        12, '555-0123', 'ufrobotics@example.com', 'RSO', 12, 3, 1),
       (11, 'UF Chess Club Tournament', 'Games', 'A friendly chess tournament.', '13:00:00', '2022-11-19', 13,
        '555-1230', 'ufchess@example.com', 'RSO', 13, 3, 1),
       (12, 'UF Environmental Club Cleanup', 'Environment', 'A campus cleanup event.', '09:00:00', '2022-11-26', 14,
        '555-2340', 'ufenv@example.com', 'RSO', 14, 3, 1);


-- Comments
INSERT INTO `comments` (`CommentID`, `UserID`, `EventID`, `Content`, `Timestamp`)
VALUES (1, 1, 1, 'Great event! Learned a lot.', '2022-11-05 20:00:00'),
       (2, 2, 1, 'Had an amazing time!', '2022-11-05 21:00:00'),
       (3, 3, 2, 'The workshop was very informative.', '2022-11-12 17:00:00'),
       (4, 4, 2, 'Loved the hands-on experience.', '2022-11-12 18:00:00'),
       (5, 5, 3, 'Fun tournament!', '2022-11-19 16:00:00'),
       (6, 6, 3, 'Great atmosphere and friendly competition.', '2022-11-19 17:00:00'),
       (7, 12, 5, 'Amazing hackathon!', '2022-11-05 22:00:00'),
       (8, 13, 5, 'Met some talented people!', '2022-11-05 23:00:00'),
       (9, 14, 6, 'The workshop was very insightful.', '2022-11-12 18:30:00'),
       (10, 15, 6, 'Great hands-on experience with robots!', '2022-11-12 19:00:00'),
       (11, 16, 7, 'Fun and exciting chess games!', '2022-11-19 16:30:00'),
       (12, 17, 7, 'Friendly competition and great atmosphere.', '2022-11-19 17:30:00'),
       (13, 37, 9, 'The hackathon was a great learning experience!', '2022-11-05 20:30:00'),
       (14, 38, 9, 'Met some amazing coders!', '2022-11-05 21:30:00'),
       (15, 39, 10, 'The robotics workshop was fantastic!', '2022-11-12 17:30:00'),
       (16, 40, 10, 'Loved building robots!', '2022-11-12 18:30:00'),
       (17, 41, 11, 'The chess tournament was so much fun!', '2022-11-19 16:45:00'),
       (18, 42, 11, 'Great people and amazing competition.', '2022-11-19 17:45:00');

-- Ratings
INSERT INTO `ratings` (`UserID`, `EventID`, `Stars`)
VALUES (1, 1, 5),
       (2, 1, 4),
       (3, 2, 4),
       (4, 2, 5),
       (5, 3, 5),
       (6, 3, 4),
       (12, 5, 5),
       (13, 5, 4),
       (14, 6, 4),
       (15, 6, 5),
       (16, 7, 5),
       (17, 7, 4),
       (37, 9, 5),
       (38, 9, 4),
       (39, 10, 4),
       (40, 10, 5),
       (41, 11, 5),
       (42, 11, 4);

INSERT INTO `ratings` (`UserID`, `EventID`, `Stars`)
VALUES (7, 1, 3),
       (8, 1, 4),
       (9, 2, 5),
       (10, 2, 4),
       (11, 3, 3),
       (18, 3, 5),
       (19, 5, 4),
       (20, 5, 5),
       (21, 6, 3),
       (22, 6, 4),
       (23, 7, 5),
       (24, 7, 4),
       (43, 9, 4),
       (44, 9, 5),
       (45, 10, 3),
       (46, 10, 4),
       (47, 11, 5),
       (48, 11, 4),
       (25, 1, 4),
       (26, 1, 5),
       (27, 2, 3),
       (28, 2, 4),
       (29, 3, 5),
       (30, 3, 4),
       (31, 5, 5),
       (32, 5, 4),
       (33, 6, 3),
       (34, 6, 4),
       (35, 7, 5),
       (36, 7, 4),
       (49, 9, 5),
       (50, 9, 4),
       (51, 10, 3),
       (52, 10, 4),
       (53, 11, 5),
       (54, 11, 4);

