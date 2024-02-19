<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/global.css">
    <link rel="stylesheet" href="/public/css/login.css">
    <title>LinkyApp Register</title>
</head>

<body>
<main class="flex-column flex-center">
    <form action="/register" method="POST" class="main-form flex-column flex-center">
        <h1 class="text-secondary text-shadow text-center">Join us today</h1>
        <div class="input-container flex-column flex-center">
            <input title="username" type="text" name="userName" class="input" placeholder="Username" minlength="3"
                   maxlength="50" required>
            <input type="email" name="email" class="input" placeholder="Email"
                   title="Email" required>
            <input type="password" name="password" class="input" placeholder="Password"
                   pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}"
                   title="Password must be at least 8 characters long, contain at least 1 lowercase letter,
                   1 uppercase letter, 1 number, and 1 special character" required>
            <input type="password" name="passwordConfirm" class="input" placeholder="Confirm password"
                   pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}"
                   title="Please enter the same password as above" required>
        </div>

        <?php if (isset($data)): ?>
            <div class="messages">
                <?php foreach ($data as $message): ?>
                    <p><?= $message; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <button class="btn-primary" type="submit" title="LinkyApp Sign Up">
            <span class="btn-primary-top">Sign Up</span>
        </button>
        <p class="text-secondary">Already have an account?
            <a href="/login" class="text-underline">Sign in</a>
        </p>
    </form>
</main>
<footer class="footer-logo flex-column flex-center">
    <a href="/index" title="LinkyApp" class="logo-text flex">
        <img src="/public/assets/svg/logo.svg" alt="LinkyApp">
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