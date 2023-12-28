<?php

use src\Helpers\ModuleRenderer;

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/public/css/global.css">
	<link rel="stylesheet" href="/public/css/index.css">
	<title>LinkyApp</title>
</head>

<body>
	<header class="flex flex-center">
		<div class="header-container flex-column">
			<h1 class="text-secondary text-shadow text-center">
				Introducing LinkyApp:<br>
				Your One-Stop Bookmark Solution
			</h1>
			<h2 class="text-secondary text-center">
				Tired of endlessly searching for your go-to websites?
				LinkyApp is here to simplify your online life and keep
				everything organized in one place.</h2>
		</div>
	</header>
	<main class="flex-column flex-center">
		<h2 class="text-secondary text-shadow text-center hide-mobile">Get Started</h2>
		<div class="btn-container flex-column flex-center">
			<a href="login" class="btn-primary" type="button" title="LinkyApp Sign In">
				<span class="btn-primary-top">Sign In</span>
			</a>
			<a href="register" class="btn-primary" type="button" title="LinkyApp Sign Up">
				<span class="btn-primary-top">Sign Up</span>
			</a>
		</div>
	</main>
	<footer class="footer-logo flex-column flex-center">
		<a href="index" title="LinkyApp" class="logo-text flex">
			<?= ModuleRenderer::renderIcon('logo') ?>
			<p>LinkyApp</p>
		</a>
		<div class="footer-policy flex">
			<p>Terms of use</p>
			<p>|</p>
			<p>Privacy policy</p>
		</div>
	</footer>
</body>

</html>