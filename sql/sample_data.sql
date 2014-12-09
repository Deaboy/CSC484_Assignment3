# Sample data for database
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE Loan;
TRUNCATE CopyBook;
TRUNCATE Book;
TRUNCATE Patron;
TRUNCATE Author;
TRUNCATE Library;
SET FOREIGN_KEY_CHECKS = 1;

# Libraries
INSERT INTO Library (libName, location, noRooms) VALUES
	('Devorakk Library', '123 Alabama Road, Alabama', 7),
	('Arzerack Library', '423 Central Ave, New York', 5),
	('Zimmerrock Halls', '821452 Mountain CT, Wyoming', 22);

# Book Authors
INSERT INTO Author ( authorName ) VALUES
	('Albert Boinks'),
	('Justin Cerval'),
	('Wayne Jinkens'),
	('Victoria, Dot'),
	('Mason, Zimmer'),
	('Tori, Nixoner');

# Library patrons
INSERT INTO Patron ( patronName, patronType ) VALUES
	('Gage Geigle', 3),
	('Marcus Berger', 4),
	('Joe Box', 1),
	('Zack David', 0),
	('Kanye, Kicks', 1),
	('David Zack', 2),
	('Ronald Berks', 2);

# Book
INSERT INTO Book ( title, noPages, authorno ) VALUES
	('My First Book', 3, 6 ),
	('Tale of Two Suns', 4031, 2),
	('Mark of a Stone Tooth', 321, 2),
	('Mysticallities of the Soul', 555, 5),
	('My Face When', 25, 6 ),
	('How to Read', 732, 4 ),
	('How to Remember', 473, 4),
	('The Day of Light', 100, 1),
	('Remember Me', 3921, 2 ),
	('Vector Notation', 532, 4);

# Copy of Books
INSERT INTO CopyBook ( libNo, bookNo, cost ) VALUES
	( 1, 1, 4 ),
	( 2, 1, 4 ),
	( 3, 1, 4 ),
	( 1, 2, 25 ),
	( 1, 2, 25 ),
	( 1, 2, 25 ),
	( 2, 2, 25 ),
	( 3, 2, 25 ),
	( 3, 2, 25 ),
	( 1, 3, 30 ),
	( 1, 3, 30 ),
	( 1, 3, 30 ),
	( 2, 3, 30 ),
	( 2, 3, 30 ),
	( 2, 3, 30 ),
	( 3, 3, 30 ),
	( 2, 4, 35 ),
	( 2, 4, 35 ),
	( 3, 4, 35 ),
	( 3, 4, 25 ),
	( 1, 5, 525 ),
	( 2, 5, 525 ),
	( 3, 5, 525 ),
	( 1, 6, 700 ),
	( 2, 6, 700 ),
	( 3, 6, 700 ),
	( 3, 7, 50 ),
	( 3, 8, 47 ),
	( 1, 9, 25 ),
	( 2, 9, 25 ),
	( 3, 9, 25 ),
	( 3, 9, 25 ),
	( 1, 10, 693 ),
	( 1, 10, 693 ),
	( 1, 10, 693 ),
	( 1, 10, 693 ),
	( 2, 10, 693 ),
	( 2, 10, 693 ),
	( 2, 10, 693 ),
	( 3, 10, 693 ),
	( 3, 10, 693 ),
	( 3, 10, 693 ),
	( 3, 10, 693 ),
	( 3, 10, 693 ),
	( 3, 10, 693 ); #45 books

# Loans
INSERT INTO Loan( copyNo, patronNo, checkOutDate, dueDate ) VALUES
	( 34, 7, '1900-01-01', '1900-01-05' ),
	( 21, 4, '2014-11-01', '2014-11-05' ),
	( 1, 1, '2014-11-02', '2014-11-06' ),
	( 5, 1, '2014-11-20', '2014-11-24' ),
	( 13, 1, '2014-11-20', '2014-11-24' ),
	( 12, 3, '2014-11-24', '2014-11-29' ),
	( 19, 2, '2014-12-07', '2014-12-12' );
