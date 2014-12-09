<?php

/**
 * Connects to database using PDO and returns the connection object.
 * Returns NULL if something goes wrong.
 */
function databaseConnect()
{
  /*
   * For privacy reasons, database connection info has been put
   * into a seperate file. This file merely defines the following
   * variables:
   *
   *  - dbHost
   *  - dbName
   *  - dbUser
   *  - dbPass
   */
  include "dbConnectionInfo.php";
  
  try
  {
    $pdo = new PDO("mysql:dbname=$dbName;host=$dbHost", $dbUser, $dbPass);
    return $pdo;
  }
  catch (PDOException $e)
  {
    return NULL;
  }
}



/**
 * Given a string and number of inspaces to indent it, will insert given
 * number of spaces after every new line and at the beginning of the string and
 * will return the result.
 */
function indent($subject, $spaces)
{
  $replace = "";
  for ($i = 0; $i < $spaces; $i++)
    $replace .= " ";
  
  return str_replace("\n", "\n$replace", $subject);
}



/**
 * Takes the results of a database query and formats the rows
 * to appear in a neat, orderly fashion in a table.
 */
function resultToTable($result)
{
  $content = "";
  
  // Build table
  ob_start();
?>

<?php if (count($result) > 0) { ?>

<table class="results">
  <tbody>
    <tr>
<?php foreach ($result[0] as $key => $val) { ?>
      <th><?php echo $key; ?></th>
<?php } ?>
    </tr>
<?php foreach ($result as $row) { ?>
    <tr>
<?php foreach ($row as $val) { ?>
      <td><?php echo $val; ?></td>
<?php } ?>
    </tr>
<?php } ?>
  </tbody>
</table>

<?php } else { ?>

<div class="no-results">No results</div>

<?php } ?>

<?php
  // Clean up and return table;
  $content .= ob_get_clean();
  return $content;
}



/**
 * Generates content for the Home page
 */
function generateHomePage()
{
  global $rootURL;
  
  // Content string
  $content = "<h1>Home</h1>\n";
  
  // Uses 3rd party class Parsedown to parse the project README file
  require_once "Parsedown.php";
  $Parsedown = new Parsedown();
  
  $content .= "<div class=\"readme\">\n"
    .$Parsedown->text(file_get_contents("README.md"))
    ."</div>\n";
  
  return $content;
}



/**
 * Generates content for the Loans page
 */
function generateLoansPage()
{
  global $rootURL;
  
  // Content string
  $content = "<h1>Loans</h1>\n";
  $patron = 0;
  
  // Attempt to connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    $content .= "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";

    return $content;
  }
  
  // Execute select query
  $query = $pdo -> prepare("SELECT * FROM Patron ORDER BY patronName");
  $query -> execute();
  $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
  $result = $query -> fetchAll();
  
  // If a patron is defined, fetch loans from database
  if (isset($_POST["patron"]))
  {
    $patron = (int) $_POST["patron"];
  }
  else
  {
    $patron = 0;
  }
  
  // Build patron selection form
  ob_start();
?>

<form action="<?php echo $rootURL; ?>?p=loans" method="post">
  <select name="patron">
    <option value="0"<?php echo ($patron == 0 ? " selected" : ""); ?> disabled>Select Patron</option>
<?php foreach ($result as $row) { ?>
    <option value="<?php echo $row['patronNo']; ?>"<?php echo ($patron == $row['patronNo'] ? " selected" : ""); ?>><?php echo $row['patronName']; ?></option>
<?php } ?>
  </select>
  <button type="submit" value="submit">Go</button>
</form>

<?php
  $content .= ob_get_clean();
  
  // If patron was selected, display table of results
  
  if ($patron > 0)
  {
    // Prepare query and execute query
    $query = $pdo -> prepare(
      "SELECT
        Book.bookNo AS Book,
        CopyBook.copyNo AS Copy,
        Book.title AS Title,
        Author.authorName AS Author,
		Book.noPages AS Pages,
		Library.libName AS Library,
        Loan.checkOutDate AS Checkout,
        Loan.dueDate AS Due
      FROM Loan
      LEFT JOIN
        CopyBook ON (CopyBook.copyNo = Loan.copyNo)
      LEFT JOIN
        Book ON (Book.bookNo = CopyBook.bookNo)
      LEFT JOIN
        Author ON (Author.authorNo = Book.authorNo)
      LEFT JOIN
        Library ON (Library.libNo = CopyBook.libNo)
      WHERE
        Loan.patronNo = :patronNo
      ORDER BY
        Book.bookNo ASC");
    $query -> bindParam(':patronNo', $patron, PDO::PARAM_INT);
    $query -> execute();
    $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
    $result = $query -> fetchAll();
    
    // Add results to content
    $content .= resultToTable($result);
  }
  else
  {
    // If no query was made, just display prompt
    ob_start();
?>
<div class="no-results">
  Select a patron from the form above to view loaned books.
</div>
<?php
    $content .= ob_get_clean();
  }
  
  // Clean up after ourselves!
  $pdo = NULL;
  return $content;
}



/**
 * Generates content for the Patrons page
 */
function generatePatronsPage()
{
  global $rootURL;
  
  // Content string
  $content = "<h1>Patrons</h1>\n";
  
  //try to connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    $content .= "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";
    
    return $content;
  }

  // Begin Query to display table of Patrons
  $query = $pdo -> prepare(
    "SELECT
      Patron.patronName as Name,
      Patron.patronNo as ID,
      Patron.patronType as Type,
      (SELECT COUNT(*) FROM Loan WHERE Loan.patronNo = Patron.patronNo) AS Loans
    FROM Patron
    ORDER BY
      Patron.patronName ASC");
  $query -> execute();
  $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
  $result = $query -> fetchAll();
  
  // Add button to create new patron
  ob_start();
?>

<a href="<?php echo $rootURL; ?>?p=addpatron">
  <button>+ Add New Patron</button>
</a>

<?php
  $content .= ob_get_clean();

  // Add results to content
  $content .= resultToTable($result);
  
  // TWICE
  ob_start();
?>

<!-- Literally exactly the same as the one above -->
<a href="<?php echo $rootURL; ?>?p=addpatron">
  <button>+ Add New Patron</button>
</a>

<?php
  $content .= ob_get_clean();
  
  // Clean up
  $pdo = NULL;
  return $content;
}



/**
 * Generates content for the New Patron page
 */
function generateAddPatronPage()
{
  global $rootURL;
  
  // Content string
  $content = "<h1>New Patron</h1>\n";

  // connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    $content .= "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";
    
    return $content;
  }

  //Test to see if user submitted a Patron
  if (isset($_POST["Patrontxt"]) and isset($_POST["Typetxt"]))
  {
    //build query
    $PatronName = $_POST["Patrontxt"];
    $PatronType = (int) $_POST["Typetxt"];
    if ($PatronName == "")
    {
      //Display Error
      ob_start();
?>
<div class="warning">
  <h1>New patron was not added</h1>
  <p>Please enter a name.</p>
</div>
<?php
      $content .= ob_get_clean();
    }
    else
    {
      //build Insert command
      $query = $pdo -> prepare(
        "INSERT INTO Patron ( patronName, patronType ) VALUES
          (:patronName, :patronType)");
      $query -> bindParam(':patronName', $PatronName, PDO::PARAM_STR, 128);
      $query -> bindParam(':patronType', $PatronType, PDO::PARAM_INT);
      $result = $query -> execute();

      //check if command successful
      if (!$result)
      {
        //if it was not display error
        ob_start();
?>
<div class="no-results">New patron was not added!</div>
<?php
        $content .= ob_get_clean();
      }
      else
      {
        //if it was display confirmation
        ob_start();
?>
<div class="no-results">New patron &quot;<?php echo $PatronName; ?>&quot; was added.</div>
<?php
        $content .= ob_get_clean();
      }
      
    }
  }
  
  
  //build form with two labels, two textboxes, and a submit button
  ob_start();
?>
<form action="<?php echo $rootURL; ?>?p=addpatron" method="post">
  <label for="Patrontxt">Patron Name</label>
  <input type="text" name="Patrontxt" placeholder="Name" /><br />
  <label for="Typetxt">Patron Type</label>
  <input type="text" name="Typetxt" placeholder="Type" value="0" /><br />
  <input type="submit" value="Submit" />
</form>
<?php
  $content .= ob_get_clean();

  // Clean up and go home!
  $pdo = NULL;
  return $content;
}



/**
 * Generate content for Books page
 */
function generateBooksPage()
{
  global $rootURL;
  
  // Content string
  $content = "<h1>Books</h1>\n";
  $search = "";
  
  // Attempt to connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    $content .= "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";
    
    return $content;
  }
  
  // If a search is defined, fetch Books from database
  if (isset($_POST["search"]))
  {
    $search = trim($_POST["search"]);
  }
  else
  {
    $search = "";
  }

  // Build Book search form
  ob_start();
?>

<form action="<?php echo $rootURL; ?>?p=books" method="post">
  <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Click here to search!" />
  <input type="submit" value="Search" />
</form>

<?php
  $content .= ob_get_clean();

  if ( $search != "" )
  {
    // Execute select query with search
    $query = $pdo -> prepare(
      "SELECT
        Book.bookNo AS Book,
        CopyBook.copyNo AS Copy,
        Book.title AS Title,
        Author.authorName AS Author,
        Book.noPages AS Pages,
        Library.libName AS Library,
        (CASE WHEN
            EXISTS (SELECT NULL FROM Loan 
            WHERE Loan.copyNo = CopyBook.copyNo)
          THEN 'No' ELSE 'Yes' END) AS Available
      FROM Book
      INNER JOIN
        CopyBook ON (CopyBook.bookNo = Book.bookNo)
      LEFT JOIN
        Author ON (Author.authorNo  =  Book.authorNo)
      LEFT JOIN
        Library ON (Library.libNo  =  CopyBook.libNo)
      WHERE
        Book.title LIKE CONCAT('%', :search, '%')
      ORDER BY
        Author.authorName ASC,
        Book.title ASC,
        Library.libName ASC");
    $query -> bindParam(":search", $search, PDO::PARAM_STR);
  }
  else
  {
    // Execute select query without search
    $query = $pdo -> prepare(
      "SELECT
        Book.bookNo AS Book,
        CopyBook.copyNo AS Copy,
        Book.title AS Title,
        Author.authorName AS Author,
        Book.noPages AS Pages,
        Library.libName AS Library,
        (CASE WHEN
            EXISTS (SELECT NULL FROM Loan 
            WHERE Loan.copyNo = CopyBook.copyNo)
          THEN 'No' ELSE 'Yes' END) AS Available
      FROM Book
      INNER JOIN
        CopyBook ON (CopyBook.bookNo = Book.bookNo)
      LEFT JOIN
        Author ON (Author.authorNo  =  Book.authorNo)
      LEFT JOIN
        Library ON (Library.libNo  =  CopyBook.libNo)
      WHERE
        Book.title LIKE CONCAT('%', :search, '%')
      ORDER BY
        Author.authorName ASC,
        Book.title ASC,
        Library.libName ASC");
    $query -> bindParam(":search", $search, PDO::PARAM_STR);
  }
  
  $query -> execute();
  $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
  $result = $query -> fetchAll();

  // Append buttons to lend out books (for those not already lent out)
  for ($i = 0; $i < sizeof($result); $i++)
  {
    $result[$i]['Action'] =
      ($result[$i]['Available'] == "Yes" ? "<a href=\"$rootURL?p=newloan&copy=".$result[$i]['Copy']."\">" : "")
      . "<button" .($result[$i]['Available'] == "No" ? " disabled" : ""). ">Loan Out</button>"
      . ($result[$i]['Available'] == "Yes" ? "</a>" : "");
  }

  // Add results to content
  $content .= resultToTable($result);
  
  // Clean up after ourselves!
  $pdo = NULL;
  return $content;
}



/**
 * Generates content for the New Loan page
 */
function generateNewLoanPage()
{
  global $rootURL;
  
  // Content string
  $content = "<h1>New Loan</h1>\n";
  $bookcopy = 0;
  
  // Attempt to connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    $content .= "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";
    
    return $content;
  }
  
  // Handle submissions
  if (isset($_POST['bookcopy']))
  {
    // First, make sure everything's there
    if (!isset($_POST['bookcopy'])
      || !isset($_POST['patron'])
      || !isset($_POST['checkoutDate'])
      || !isset($_POST['dueDate']))
    {
      $content .= "<div class=\"warning\">
  <h1>Form Submission Failed</h1>
  <p>Something was weird about your submission. Please try again.</p>
</div>";
    }
    else
    {
      // Import form values
      $bookcopy = (int) $_POST['bookcopy'];
      $patron = (int) $_POST['patron'];
      $checkoutDate = $_POST['checkoutDate'];
      $dueDate = $_POST['dueDate'];

      $query = $pdo -> prepare(
      "SELECT
        (SELECT COUNT(*) FROM Loan WHERE patronNo = :patronNo) AS patronLoans,
        (SELECT EXISTS(SELECT NULL FROM Loan WHERE copyNo = :copyNo)) AS loaned,
        (SELECT EXISTS(SELECT NULL FROM CopyBook WHERE copyNo = :copyNo)) AS copyExists,
        (SELECT EXISTS(SELECT NULL FROM Patron WHERE patronNo = :patronNo)) AS patronExists");
      $query -> bindParam(':patronNo', $patron, PDO::PARAM_INT);
      $query -> bindParam(':copyNo', $bookcopy, PDO::PARAM_INT);
      $query -> execute();
      $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
      $result = $query -> fetchAll();
      
      $fail = false;
      
      // Make sure patron exists
      if (!$fail && $result[0]['patronExists'] == 0)
      {
        $fail = true;
        $content .= "<div class=\"warning\">
  <h1>Form Submission Failed</h2>
  <p>That book copy doesn't exist. What're you trying to pull?</p>
</div>";
      }
      
      // Make sure book exists
      if (!$fail && $result[0]['copyExists'] == 0)
      {
        $fail = true;
        $content .= "<div class=\"warning\">
  <h1>Form Submission Failed</h2>
  <p>That book copy doesn't exist. What're you trying to pull?</p>
</div>";
      }
      
      // Lastly, make sure the book isn't already loaned
      if (!$fail && $result[0]['loaned'] == 1)
      {
        $fail = true;
        $content .= "<div class=\"warning\">
  <h1>Form Submission Failed</h2>
  <p>It appears that this book has already been loaned out.</p>
</div>";
      }
      
      // Make sure patron doesn't have 3 books checked out already
      if (!$fail && $result[0]['patronLoans'] >= 3)
      {
        $fail = true;
        $content .= "<div class=\"warning\">
  <h1>Form Submission Failed</h2>
  <p>That patron already has at least 3 books checked out.</p>
</div>";
      }
      
      // Attempt to insert the entry
      if (!$fail)
      {
        $query = $pdo -> prepare(
        "INSERT INTO Loan
          (copyNo, patronNo, checkoutDate, dueDate)
        VALUES
          (:copyNo, :patronNo, :checkoutDate, :dueDate)");
        
        // Bind params and execute query
        $query -> bindParam(':copyNo', $bookcopy, PDO::PARAM_INT);
        $query -> bindParam(':patronNo', $patron, PDO::PARAM_INT);
        $query -> bindParam(':checkoutDate', $checkoutDate, PDO::PARAM_STR);
        $query -> bindParam(':dueDate', $dueDate, PDO::PARAM_STR);
        $result = $query -> execute();
        
        // Check if it worked
        if (!$result)
        {
          $content .= "<div class=\"warning\">
  <h1>Insertion failed!</h2>
  <p>Something went wrong. Please try again.</p>
</div>";
        }
        else
        {
          ob_start();
?>
<div class="no-results">New loan created!</div>
<a href="<?php echo $rootURL; ?>?p=books">
  <button>Return to Books</button>
</a>
<?php
          $content .= ob_get_clean();
          
          // Return home!
          $pdo = NULL;
          return $content;
        }
      }
    }
  }
  
  // Get the copy of the book we're lending out
  if (isset($_GET['copy']))
  {
    $bookcopy = (int) $_GET['copy'];
  }
  else
  {
    $content .= "<div class=\"warning\">
  <h1>No book copy selected</h1>
  <p>Please select a book copy from the <a href=\"$rootURL?p=books\">Books page</a>.</p>
</div>";
    
    // Clean up and return
    $pdo = NULL;
    return $content;
  }
  
  // Execute select query
  $query = $pdo -> prepare(
  "SELECT
    Book.bookNo AS Book,
    CopyBook.copyNo AS Copy,
    Book.title AS Title,
    Author.authorName AS Author,
    Book.noPages AS Pages,
    Library.libName AS Library
  FROM CopyBook
  LEFT JOIN
    Book ON (Book.bookNo = CopyBook.bookNo)
  LEFT JOIN
    Author ON (Author.authorNo = Book.authorNo)
  LEFT JOIN
    Library ON (Library.libNo = CopyBook.libNo)
  WHERE
    CopyBook.copyNo = :bookcopy");
  $query -> bindParam(':bookcopy', $bookcopy, PDO::PARAM_INT);
  $query -> execute();
  $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
  $result = $query -> fetchAll();
  
  ob_start();
?>

<h2>Book to be loaned</h2>

<?php
  $content .= ob_get_clean();
  
  $content .= resultToTable($result);
  
  // Get patrons from database too
  $query = $pdo -> prepare("SELECT * FROM Patron ORDER BY patronName");
  $query -> execute();
  $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
  $result = $query -> fetchAll();
  
  // Build loan information form
  ob_start();
?>

<h2>Loan Information</h2>

<form action="<?php echo $rootURL; ?>?p=newloan&copy=<?php echo $bookcopy; ?>" method="post">
  <label for="patron">Patron</label>
  <select name="patron">
    <option value="0" disabled selected>Select Patron</option>
<?php foreach ($result as $row) { ?>
    <option value="<?php echo $row['patronNo']; ?>"><?php echo $row['patronName']; ?></option>
<?php } ?>
  </select><br />
  <label for="checkoutDate">Checkout Date</label>
  <input type="text" name="checkoutDate" placeholder="YYYY-MM-DD" /><br />
  <label for="dueDate">Due Date</label>
  <input type="text" name="dueDate" placeholder="YYYY-MM-DD" /><br />
  <input type="hidden" name="bookcopy" value="<?php echo $bookcopy; ?>" />
  <input type="submit" value="Submit" />
</form>

<?php
  $content .= ob_get_clean();
  
  // Clean up and go home
  $dbo = NULL;
  return $content;
}

?>
