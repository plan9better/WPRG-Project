<!DOCTYPE html>
<html>
<?php include "/var/www/html/project/Bus.php";
// Included later in nav.php
// include "/var/www/html/project/User.php";
?>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="./static/css/login_wtf.css">
    <link rel="stylesheet" href="./static/css/nav.css">
</head>

<body>
    <?php include "/var/www/html/project/nav.php"; ?>

    <div id="container">
    <fieldset>
        <legend>
            Register
        </legend>
        <form id="form" method="post" action="register.php" aria-label="register form">
            <input type="text" name="login" placeholder="Login" required aria-label="username input">
            <input type="password" name="password" placeholder="Password" required aria-label="password input">
            <input type="password" name="conf_password" placeholder="Confirm password" required aria-label="confirm password input">
            <input type="submit" value="Submit" id="submit" name="submit" aria-label="submit button">
        </form>
        <p>Already have an account? <a href="login.php" aria-label="Already have an account? go to login page">Click here</a>.</p>
    </fieldset>
    </div>
<?php if (isset($_POST["submit"])) {
    $user = new User($_COOKIE["bussession"]);
    if (
        isset($_COOKIE["bussession"]) &&
        $user->getUid() != false
    ){
        echo "<div id='error'>You are already logged in</div>";
        echo "</body></html>";
        exit();
    }

    if (
        !ctype_alnum($_POST["login"]) ||
        strlen($_POST["login"]) < 2 ||
        strlen($_POST["login"]) > 32
    ) {
        echo "<div id='error'>Illegal username. Make sure you enter only letters and numbers between 2 and 32 characters.</div>";
        echo "</body></html>";
        exit();
    }
    if ($_POST["password"] != $_POST["conf_password"]) {
        echo "<div id='error'>Passwords are not the same.</div>";
        echo "</body></html>";
        exit();
    }
    $name = htmlentities($_POST["login"]);
    $password = hash("sha256", $_POST["password"]);
    // for debugging
    // $name = "admin";
    // $password = hash("sha256", "assword");

    $user = User::checkUserExists($name);
    if ($user) {
        echo "<div id='error'>An account with that login already exists.</div>";
        echo "</body></html>";
        exit();
    } else {
        $ok = User::addUser($name, $password);
        if ($ok) {
            $user = User::login($name, $password);
            // expires in 7 days
            // "/" -> available on the entire website
            echo "<div id='success'>Success. Refresh or go home to see results.</div>";
            setcookie("bussession", $user["session"], time() + 86400 * 7, "/");
        } else {
            echo "<div id='error'>Something went wrong and we could not create your account. Please try again.</div>";
        }
    }
} ?>
</div>
</body>

</html>
