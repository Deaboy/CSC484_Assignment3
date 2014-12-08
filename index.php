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
 
 $rootURL = basename($_SERVER['PHP_SELF']);

// Title for the page
$pageTitle = "CSC 484 - Assignment 3";

// Main header for the page
$pageHeader = "<h1>Hello, World!</h1>";

// Main content for the page
$pageContent = "<p>How are you doing today?</p>";

// Main footer for the page
$pageFooter = "";

// Navigation for the page
$pageNavigation = "<ul class=\"nav\">
	<li><a href=\"$rootURL\">Home</a></li>
	<li>Item 2</li>
	<li>Item 3</li>
	<li>Item 4</li>
</ul>";


/*
 *
 *
 *
 *
 * Do our processing here
 *
 *
 *
 *
 */

// Display everything formatted nicely in the template
include "template.php";

?>

