# Create table queries for database
# Libraries
CREATE TABLE IF NOT EXISTS Library (
	libNo BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	libName VARCHAR(128) NOT NULL DEFAULT "",
	location VARCHAR(256) NOT NULL DEFAULT "",
	noRooms INT NOT NULL DEFAULT 0
);


# Book authors
CREATE TABLE IF NOT EXISTS Author (
	authorNo BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	authorName VARCHAR(128) NOT NULL DEFAULT ""
);



# Library patrons
CREATE TABLE IF NOT EXISTS Patron (
	patronNo BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	patronName VARCHAR(128) NOT NULL DEFAULT "",
	patronType INT NOT NULL DEFAULT 0
);



# Library Books
CREATE TABLE IF NOT EXISTS Book (
	bookNo BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR(256) NOT NULL DEFAULT "",
	noPages BIGINT NOT NULL DEFAULT 0,
	authorNo BIGINT NOT NULL,

	CONSTRAINT fk_book_author
		FOREIGN KEY (authorNo)
		REFERENCES Author(authorNo)
		ON DELETE CASCADE ON UPDATE CASCADE
);



# Copies of library books (instances of class :P)
CREATE TABLE IF NOT EXISTS CopyBook (
	copyNo BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	libNo BIGINT NOT NULL,
	bookNo BIGINT NOT NULL,
	cost INT NOT NULL DEFAULT 0,
	
	CONSTRAINT fk_copybook_library
		FOREIGN KEY (libNo)
		REFERENCES Library(libNo)
		ON DELETE CASCADE ON UPDATE CASCADE,
	
	CONSTRAINT fk_copybook_book
		FOREIGN KEY (bookNo)
		REFERENCES Book(bookNo)
		ON DELETE CASCADE ON UPDATE CASCADE
);



# Patrons loaning copies of books from libraries
CREATE TABLE IF NOT EXISTS Loan (
	loanNo BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	copyNo BIGINT NOT NULL,
	patronNo BIGINT NOT NULL,
	checkOutDate DATE NOT NULL,
	dueDate DATE NOT NULL,
	
	UNIQUE INDEX (copyNo),
	INDEX (patronNo),

	CONSTRAINT fk_loan_copybook
		FOREIGN KEY (copyNo)
		REFERENCES CopyBook(copyNo)
		ON DELETE CASCADE ON UPDATE CASCADE,

	CONSTRAINT fk_loan_patron
		FOREIGN KEY (patronNo)
		REFERENCES Patron(patronNo)
		ON DELETE CASCADE ON UPDATE CASCADE
);


