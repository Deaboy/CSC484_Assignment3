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
  
  $con = mysql_connect($dbHost, $dbUser, $dbPass, $dbName);
  
  if (mysqli_connect_errno())
    return NULL;
  else
    return $con;
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
