<?php

use LinkyApp\Views\Modules\FooterModule;
use LinkyApp\Views\Modules\IconModule;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkyApp Dashboard</title>
    <link rel="stylesheet" href="/public/css/global.css">
    <link rel="stylesheet" href="/public/css/dashboard.css">
    <script src="/public/js/dashboard/dashboard.js" type="module"></script>
</head>

<body class="flex">
<nav id="nav-mobile" class="flex-column hide-desktop hide-on-scroll collapse">
    <div class="nav-container flex-column">
        <div class="nav-menu flex">
            <button id="btn-mobile-menu" class="btn-nav btn-hamburger">
                <span class="bar"></span>
            </button>
            <div class="groups-buttons"></div>
        </div>
        <div id="nav-content" class="nav-content flex-column flex-center">
            <div id="nav-user-info"></div>
            <div class="line-horizontal-secondary"></div>
            <a id="btn-mobile-settings" href="#" class="page-settings btn-primary btn-nav btn-page" type="button"
               title="Settings">
                <span class="btn-primary-top">Settings</span>
            </a>
            <a id="btn-mobile-logout" href="logout" class="btn-logout btn-primary btn-nav" type="button" title="Log 
            out">
                <span class="btn-primary-top">Log out</span>
            </a>
        </div>
        <div id="nav-footer" class="footer-logo flex-column flex-center">
            <a href="index" title="LinkyApp" class="logo-text flex">
                <?= IconModule::render('logo') ?>
                <p>LinkyApp</p>
            </a>
        </div>
    </div>
</nav>
<aside id="sidebar-container" class="hide-mobile">
    <nav class="flex-column">
        <ul class="flex-column">
            <li>
                <a class="page-home btn-page active" href="#" title="Home">
                    <?= IconModule::render('home') ?>
                </a>
            </li>
            <li>
                <a class="page-shared btn-page" href="#" title="Shared">
                    <?= IconModule::render('share') ?>
                </a>
            </li>
            <li>
                <a class="page-settings btn-page" href="#" title="Settings">
                    <?= IconModule::render('settings') ?>
                </a>
            </li>
            <li>
                <a class="btn-logout" href="logout" title="Log out">
                    <?= IconModule::render('logout') ?>
                </a>
            </li>
        </ul>
        <a href="index" title="LinkyApp">
            <?= IconModule::render('logo') ?>
        </a>
    </nav>
</aside>
<main>
    <div id='page-spinner' class="flex flex-center hidden"><span class='loader'></span></div>
    <div class="page"></div>
</main>
<?= FooterModule::render(); ?>
</body>

</html>