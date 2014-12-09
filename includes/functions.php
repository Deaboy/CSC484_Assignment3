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



function generateHomePage()
{
  global $rootURL;
  
  // Content string
  $content = "";
  
  // Uses 3rd party class Parsedown to parse the project README file
  require_once "Parsedown.php";
  $Parsedown = new Parsedown();
  $content .= $Parsedown->text(file_get_contents("README.md"));
  
  return $content;
}



function generateLoansPage()
{
  global $rootURL;
  
  // Content string
  $content = "";
  $patron = 0;
  
  // Attempt to connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    return "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";
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
    <option value="0"<?php echo ($patron == 0 ? " selected" : ""); ?>></option>
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
        Loan.checkOutDate AS Checkout,
        Loan.dueDate AS Due
      FROM Loan
      LEFT JOIN
        CopyBook ON (CopyBook.copyNo = Loan.copyNo)
      LEFT JOIN
        Book ON (Book.bookNo = CopyBook.bookNo)
      LEFT JOIN
        Author ON (Author.authorNo = Book.authorNo)
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



function generatePatronsPage()
{
  global $rootURL;
  
  // Content string
  $content = "";
  
  return $content;
}



function generateBooksPage()
{
  global $rootURL;
  
  // Content string
  $content = "";
  
  return $content;
}

function generateNewLoanPage()
{
  global $rootURL;
  
  // Content string
  $content = "";
  global $rootURL;
  
  // Content string
  $content = "";
  $bookcopy = 0;
  
  // Attempt to connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    return "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";
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
      content .= "<div class=\"warning\">
  <h1>Form Submission Failed</h2>
  <p>Something was weird about your submission. Please try again.</p>
</h1>";
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
        (SELECT COUNT(*) FROM Loan WHERE patronNo = :patron) AS loanCount");
      $query -> bindParam(':patron', $patron, PDO::PARAM_INT);
      $query -> execute();
      $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
      $result = $query -> fetchAll();
      
      // Handle cases where 
      
      // Handle cases where patron has too many books checked out already
      if ($result[0]['c'] >= 3)
      {
      content .= "<div class=\"warning\">
  <h1>Form Submission Failed</h2>
  <p>That patron already has at least 3 books checked out.</p>
</h1>";
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
    return "<div class=\"warning\">
  <h1>No book copy selected</h1>
  <p>Please select a book copy from the <a href=\"$rootURL?p=books\">Books page</a>.</p>
</div>";
  }
  
  // Execute select query
  $query = $pdo -> prepare(
  "SELECT
    Book.bookNo AS Book,
    CopyBook.copyNo AS Copy,
    Book.title AS Title,
    Author.authorName AS Author,
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

<h1>New Loan</h1>
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
  <label>Patron
    <select name="patron">
      <option value="0"<?php echo ($patron == 0 ? " selected" : ""); ?>></option>
<?php foreach ($result as $row) { ?>
      <option value="<?php echo $row['patronNo']; ?>"<?php echo ($patron == $row['patronNo'] ? " selected" : ""); ?>><?php echo $row['patronName']; ?></option>
<?php } ?>
    </select>
  </label>
  <label>Checkout Date
    <input type="text" name="checkoutDate" placeholder="YYYY-MM-DD" />
  </label>
  <label>Due Date
    <input type="text" name="dueDate" placeholder="YYYY-MM-DD" />
  </label>
  <input type="hidden" name="bookcopy" value="<?php echo $bookcopy; ?>" />
  <button type="submit" value="submit">Submit</button>
</form>

<?php
  $content .= ob_get_clean();
  
  // Clean up and go home
  $dbo = NULL;
  return $content;
}

?>
