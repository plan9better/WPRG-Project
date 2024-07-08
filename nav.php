<?php
include "/var/www/html/project/User.php"; ?>
<nav>
    <a href="index.php">Home</a>
<?php 
$user = new User($_COOKIE["bussession"]);
if (
    isset($_COOKIE["bussession"]) &&
    $user->getUid() != false

) {
    echo '<a href="account.php">Account</a>';
} else {
    echo '<a href="login.php">Login</a>';
} ?>
</nav>
