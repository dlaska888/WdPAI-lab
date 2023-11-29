<?php

declare(strict_types=1);

require_once 'src/Router.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Router::get('', DefaultController::class);
Router::get('index', DefaultController::class);
Router::get('login', SecurityController::class);
Router::get('register', SecurityController::class);
Router::get('dashboard', DashboardController::class);

//Router::run($path);

require_once "src/repos/UserRepo.php";
require_once "src/repos/LinkRepo.php";
require_once "src/repos/LinkGroupRepo.php";
require_once "src/enums/UserRole.php";

// Instantiate repositories
$userRepo = new UserRepo();
$linkRepo = new LinkRepo();
$linkGroupRepo = new LinkGroupRepo();

// Generate a new user
$newUser = new LinkyUser(
    user_name: 'john_doe',
    email: 'john@example.com',
    password_hash: 'hashed_password'
);

// Insert the new user
$newUser = $userRepo->insert($newUser);
echo "Inserted User: " . json_encode($newUser) . "\n";

// Generate a new link group for this user
$newLinkGroup = new LinkGroup(
    user_id: $newUser->user_id,
    name: 'My Links',
    date_created: new DateTime()
);

// Insert the new link group
$newLinkGroup = $linkGroupRepo->insert($newLinkGroup);
echo "Inserted Link Group: " . json_encode($newLinkGroup) . "\n";

// Generate a new link for this group of links
$newLink = new Link(
    link_group_id: $newLinkGroup->link_group_id,
    title: 'Sample Link',
    url: 'https://youtube.com'
);

// Insert the new link
$newLink = $linkRepo->insert($newLink);
echo "Inserted Link: " . json_encode($newLink) . "\n";

// Test UserRepo
echo "\nTesting UserRepo:\n";

// Find the user by ID
$foundUserById = $userRepo->findById($newUser->user_id);
echo "Found User by ID: " . json_encode($foundUserById) . "\n";

// Find the user by username
$foundUserByUsername = $userRepo->findByUserName('john_doe');
echo "Found User by Username: " . json_encode($foundUserByUsername) . "\n";

// Test LinkGroupRepo
echo "\nTesting LinkGroupRepo:\n";

// Find the link group by ID
$foundLinkGroupById = $linkGroupRepo->findById($newLinkGroup->link_group_id);
echo "Found Link Group by ID: " . json_encode($foundLinkGroupById) . "\n";

// Test LinkRepo
echo "\nTesting LinkRepo:\n";

// Find the link by ID
$foundLinkById = $linkRepo->findById($newLink->link_id);
echo "Found Link by ID: " . json_encode($foundLinkById) . "\n";
echo $foundLinkById->url;