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
				<?php echo $pageHeader; ?>
			
			</header>
			<div class="content-wrapper">
				<article class="content">
				
					<!-- Page main content here -->
					<?php echo $pageContent; ?>
				
				</article>
			</div>
			<footer class="footer">
			
				<!-- Page footer here -->
				<?php echo $pageFooter; ?>
			
			</footer>
		</div>
		
	</body>
</html>
