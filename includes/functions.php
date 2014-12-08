<?php

/**
 * Connects to database using mysqli and returns the connection object.
 * (Must be closed afterwards!) Returns NULL if something goes wrong.
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
  
  $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
  
  if (mysqli_connect_errno())
    return NULL;
  else
    return $mysqli;
}



// Because our school's computers are missing some pretty important stuff
require_once "extra_functions.php";



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

<?php if ($row = iimysqli_result_fetch_array($result)) { ?>

<table class="results">
  <tbody>
    <tr>
<?php foreach ($row as $key => $val) { ?>
      <th><?php echo $key; ?></th>
<?php } ?>
    </tr>
<?php do { ?>
    <tr>
<?php foreach ($row as $val) { ?>
      <td><?php echo $val; ?></td>
<?php } ?>
    </tr>
<?php } while ($row = iimysqli_result_fetch_array($result)); ?>
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
  // Content string
  $content = "";
  $patron = 0;
  
  // Attempt to connect to database
  $mysqli = databaseConnect();
  if ($mysqli == NULL)
  {
    return "<h1>Database error</h1><p>Failed to connect to database.</p>";
  }
  
  // Execute select query
  $query = $mysqli -> prepare("SELECT * FROM Patron ORDER BY patronName");
  $query -> execute();
  $result = iimysqli_stmt_get_result($query);
  
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
<?php while ($row = iimysqli_result_fetch_array($result)) { ?>
    <option value="<?php echo $row['patronNo']; ?>"<?php echo ($patron == $row['patronNo'] ? " selected" : ""); ?>><?php echo $row['patronName']; ?></option>
<?php } ?>
  </select>
  <button type="submit" value="submit">Go</button>
</form>

<?php
  $content .= ob_get_clean();
  $result -> free();
  
  // If patron was selected, display table of results
  
  if ($patron > 0)
  {
    // Prepare query and execute query
    $query = $mysqli -> prepare(
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
        Loan.patronNo = ?
      ORDER BY
        Book.bookNo ASC");
    $query -> bind_param('i', $patron);
    $query -> execute();
    $result = iimysqli_stmt_get_result($query);
    
    // Add results to content
    $content .= resultToTable($result);
    
    // Clean up after ourselves!
    $result -> free();
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
  $mysqli -> close();
  return $content;
}



function generatePatronsPage()
{
  // Content string
  $content = "";
  
  return $content;
}



function generateBooksPage()
{
  // Content string
  $content = "";
  
  return $content;
}

?>
