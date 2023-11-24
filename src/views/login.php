<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/global.css">
    <link rel="stylesheet" href="/public/css/login.css">
    <title>LinkyApp - Login</title>
</head>

<body>
<main class="flex-column flex-center">
    <form action="login" method="POST" class="main-form flex-column flex-center">
        <h1 class="text-secondary text-shadow text-center">Welcome back</h1>
        <div class="input-container flex-column flex-center">
            <input type="text" name="email" class="input" placeholder="Email or username" required>
            <input type="password" name="password" class="input" placeholder="Password" required>
        </div>
        <div class="messages">
            <?php
            if (isset($messages)) {
                foreach ($messages as $message) {
                    echo $message;
                }
            }
            ?>
        </div>
        <button class="btn-primary" type="submit" title="LinkyApp Sign In">
            <span class="btn-primary-top">Sign In</span>
        </button>
        <p class="text-secondary">Don't have an account? <a href="register" class="text-underline">Sign up</a>
        </p>
    </form>
</main>
<footer class="footer-logo flex-column flex-center">
    <a href="index" title="LinkyApp" class="logo-text flex">
        <img src="/public/assets/svg/logo1.svg" alt="LinkyApp">
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