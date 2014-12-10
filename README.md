# CSC484 Assignment3
## Authors
Johnathan Ackerman, Daniel Andrus, Andrew Koc

## Description
A class project to create a web interface for manipulating a simple library
database. Users are able to view patrons currently in the database, view
and search through all book in the database, and view books loaned out by
specific patrons. In addition, users can add new patrons and loan out copies
of books to individual patrons.

The GitHub repository for this project can be found
[here](https://github.com/Deaboy/CSC484_Assignment3).

## Instructions
### Creating Tables and Sample Data
The database tables and sample data will have to be manually created using the
files provided in this project. The file to create the necessary tables is
/sql/create_tables.sql, and the file to fill these tables with sample data
is /sql/sample_data.sql.

### Database Connection Configuration
Due to security concerns and the fact that this project is made public on
GitHub, the connection info to the database has not been included with this
project. To add connection info, create a file named "dbConnectionInfo.php"
and place it in the "includes" folder. In this file, you will need to use
PHP to set the following variables according the database's connection info.

- $dbHost (The address of the database host)
- $dbName (The name of the database)
- $dbUser (The user to log into the database)
- $dbPass (The password used to connect to the databse)

### View All Patrons
To **view all patrons**, click "Patrons" in the main menu. There, you will see
a complete list of all patrons in the database in alphabetical order.

### Add a New Patron
To **add a new patron**, click "Patrons" in the main menu, then "+ Add New
Patron". From here, fill out the form with the patron's name and type, then
click "Submit." You will be told if the operaton was successful or not.

### View Books Loaned by a Patron
To **view books loaned by a patron**, click "Loans" in the main menu. From
here, select the name of the patron you desire from the drop-down menu and
click go. The selected patron's books will appear below.

### View All Books or Search Books
To **view all book copies** or to **search through book copies in all libraries**,
click "Books" in the main menu. Here, you will see a list of all copies of
books in all libraries, their titles, ID numbers, author names, number of pages,
their home library, and whether or not they are currently available (loaned
out). Enter part or all of the title of the book you are searching for in
the text box at the top and click "Search". The table below will update to
show all copies of books whose titles patially or wholly match your query.

### Loan out a Book
To **loan out a book**, click "Books" in the main menu. From here, you can search
for the copy of the book you wish to loan out by typing the book's title into
the search bar at the top. If the book is currenlty available (not loaned
out), the "Loan Out" button to the right of the book's entry will be acive.
Click "Loan Out" next to the book you wish to loan out. You will be taken
to the "New Loan" page where you will see the information of the book you
are about to loan out. Select the name of the patron you wish to loan the
book to from the drop-down menu, enter the checkout date and the due date in
ISO 8601 format (e.g. YYYY-MM-DD). When you are ready to loan out the book,
click "Submit." You will be prompted if the operation was successful or not.

## Third-Party Libraries
All files in this project **except for one** were written by had by Johnathan
Ackerman, Daniel Andrus, and Andrew Koc. We use the third-party library
[Parsedown](http://parsedown.org) to parse the contents of this README file
from [Markdown](http://daringfireball.net/projects/markdown/) to HTML. More
information about Markdown can be found at
[http://daringfireball.net/projects/markdown/](http://daringfireball.net/projects/markdown/),
and information about Parsedown can be found at
[http://parsedown.org](http://parsedown.org). We did not create Markdown,
nor did we create Parsedown; Markdown and Parsedown are property of their
respective copyright holders.
