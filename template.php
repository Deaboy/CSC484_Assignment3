<?php
/**
 * Template file
 *
 * Please follow the following guidelines:
 *  - Lowercase tags only
 *  - Use 2 spaces for indentation (not tabs)
 *  - Split long strings of text into multiple lines
 *  - No inline styles unless absolutely necessary
 *  - Meaningful indentifiers with consistent capitalization
 *  - Try-catches over critical PHP areas (e.g. database accesses)
 */
?>

<!DOCTYPE html>
<html>
  <head>

    <title><?php echo $pageTitle; ?></title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="css/styles.css" />

    <!-- Scripts -->
    <script type="text/javascript" src="js/scripts.js"></script>

  </head>
  <body>

    <div class="page">
      <header class="header">

        <!-- Page header here -->
        <?php echo indent($pageHeader, 8), "\n"; ?>

        <!-- Main navigation here -->
        <?php echo indent($pageNavigation, 8), "\n"; ?>

      </header>
      <div class="content-wrapper">
        <article class="content">

          <!-- Page main content here -->
          <?php echo indent($pageContent, 10), "\n"; ?>

        </article>
      </div>
      <footer class="footer">

        <!-- Page footer here -->
        <?php echo indent($pageFooter, 8), "\n"; ?>

        <!-- Footer navigation here -->
        <?php echo indent($pageNavigation, 8), "\n"; ?>

      </footer>
    </div>

  </body>
</html>
