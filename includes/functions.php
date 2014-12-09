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
  
  //try to connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    return "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";
  }

  // Begin Query to display table of Patrons
  $query = $pdo -> prepare(
    "SELECT
      Patron.patronName as Name,
      Patron.patronNo as ID,
      Patron.patronType as Type
    FROM Patron
    ORDER BY
      Patron.patronName ASC");
  $query -> execute();
  $result = $query -> setFetchMode(PDO::FETCH_ASSOC);
  $result = $query -> fetchAll();

  // Add results to content
  $content .= resultToTable($result);
  
  // Add button to create new patron
  ob_start();
?>


<a href="<?php echo $rootURL; ?>?p=addpatron">
  <button>+ Add New Patron</button>
</a>

<?php
  $content .= ob_get_clean();
  
  // Clean up
  $pdo = NULL;
  return $content;
}

function generateAddPatronPage()
{
  global $rootURL;
  
  // Content string
  $content = "";

  // connect to database
  $pdo = databaseConnect();
  if ($pdo == NULL)
  {
    return "<div class=\"warning\">
  <h1>Database error</h1>
  <p>Failed to connect to database.</p>
</div>";
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
<div class="no-results">New patron was not added. Please enter a name.</div>
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
  <input type="text" name="Patrontxt" placeholder="Name" />
  <label for="Typetxt">Patron Type</label>
  <input type="text" name="Typetxt" placeholder="Type" value="0" />
  <input type="submit" value="Submit" />
</form>
<?php
  $content .= ob_get_clean();

  // Clean up and go home!
  $pdo = NULL;
  return $content;
}

function generateBooksPage()
{
  global $rootURL;
  
  // Content string
  $content = "";

  return $content;
}

?>
