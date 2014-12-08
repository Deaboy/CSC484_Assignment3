<?php
/**
 * CSC 484 - Assignment 3
 * Library database manager
 *
 * Authors:
 * Johnathan Ackerman, Daniel Andrus, Andrew Koc
 *
 * Description:
 * This web app provides a convenient interface for working with a library
 * database.
 */

$rootURL = dirname($_SERVER['PHP_SELF']);

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
}

// Display everything formatted nicely in the template
include "template.php";

?>

