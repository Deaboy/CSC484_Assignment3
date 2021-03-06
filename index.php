<?php
/**
 * CSC 484 - Assignment 3
 * Library database manager
 *
 * Authors:
 * Johnathan Ackerman, Daniel Andrus, Andrew Koc
 *
 * Description:
 * A class project to create a web interface for manipulating a simple library
 * database. Users are able to view patrons currently in the database, view
 * and search through all book in the database, and view books loaned out by
 * specific patrons. In addition, users can add new patrons and loan out copies
 * of books to individual patrons.
 *
 * See README.md for more information, including usage and third-party software.
 */

// Enable error reporting if not already enabled
ini_set('display_startup_errors', 1); 
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get base URL
$rootURL = dirname($_SERVER['PHP_SELF']) . "/";

// Include functions file
require_once "includes/functions.php";

// Title for the page
$pageTitle = "CSC 484 - Assignment 3";

// Main header for the page
$pageHeader = "<h1>CSC 484 - Assignment 3</h1>
<h2>Johnathan Ackerman, Daniel Andrus, &amp; Andrew Koc</h2>";

// Main content for the page
$pageContent = "<p>How are you doing today?</p>";

// Main footer for the page
$pageFooter = "";

// Navigation for the page
$pageNavigation = "<ul class=\"nav\">
  <li><a href=\"$rootURL\">Home</a></li>
  <li><a href=\"$rootURL?p=loans\">Loans</a></li>
  <li><a href=\"$rootURL?p=patrons\">Patrons</a></li>
  <li><a href=\"$rootURL?p=books\">Books</a></li>
</ul>";

// Based on the url, determine which page to display
$page = isset($_GET["p"]) ? $_GET["p"] : "home";
switch($page)
{
default:
case "home":
  $pageContent = generateHomePage();
  break;
  
case "loans":
  $pageContent = generateLoansPage();
  break;
  
case "patrons":
  $pageContent = generatePatronsPage();
  break;
  
case "books":
  $pageContent = generateBooksPage();
  break;

case "newloan":
  $pageContent = generateNewLoanPage();
  break;

case "addpatron":
  $pageContent = generateAddPatronPage();
  break;
}

// Display everything formatted nicely in the template
include "template.php";

?>

