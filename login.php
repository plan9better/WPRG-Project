    <!DOCTYPE html>
<html>
<?php include "/var/www/html/project/Bus.php";
// Included later in nav.php
// include "/var/www/html/project/User.php";
?>
<head>
    <link rel="stylesheet" href="static/css/nav.css">
    <link rel="stylesheet" href="static/css/login_wtf.css">
    <title>Login</title>
</head>

<body>
    <?php include "/var/www/html/project/nav.php"; ?>

    <div id="container">
    <fieldset>
        <legend>
            Login
        </legend>
        <form id="form" method="post" action="login.php" aria-label="login form">
            <input type="text" name="login" placeholder="Username" required aria-label="login username input">
            <input type="password" name="password" placeholder="Password" required aria-label="login password input">
            <input type="submit" value="Submit" id="submit" name="submit" aria-label="login submit button">
        </form>
        <p>Don't have an account yet? <a href="register.php">Click here</a>.</p>
    </fieldset>
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
    $name = htmlentities($_POST["login"]);
    $password = hash("sha256", $_POST["password"]);
    // for debugging
    // $name = "admin";
    // $password = hash("sha256", "assword");
    $userlogin = $user->login($name, $password);
    if ($userlogin == null || $userlogin == []) {
        echo "<div id='error'>Those credentials don't seem to match, please try agian.</div>";
        echo "</body></html>";
        exit();
    } else {
        // expires in 7 days
        // "/" -> available on the entire website
        setcookie("bussession", $userlogin["session"], time() + 86400 * 7, "/");
        echo "<div id='success'>Success. Refresh or go Home.</div>";
    }
} ?>
    </div>
</body>

</html>
