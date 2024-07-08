<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
    <link rel="stylesheet" href="static/css/account_wtf.css">
    <link rel="stylesheet" href="static/css/nav.css">
</head>
<body>
<?php include "/var/www/html/project/nav.php";
include "/var/www/html/project/Bus.php"
 ?>
<div id="container">
    <form method="post" action="account.php">
        <input type="submit" name="logout" id="logout" value="Logout">
    </form>
    <?php
    
        $user = new User('66793de1181b0');
        $user->addFav('122');
    
    ?>

        <div id="table">
            <h1>Your favourites</h1>
            <form id="form" method="post" action="account.php">
                <label for="bus">Add to favs</label>
                <select id="bus" name="bus">
                    <?php foreach (Bus::getUniqueActive() as $k => $v) {
                        echo "<option>" . $k . "</option>";
                    } ?>
                </select>
                <input type="submit" value="add">
            </form>
            <table>
                <tr>
                    <td>Name</td>
                    <td>Type</td>
                </tr>
                <?php
                    $user = new User('66793de1181b0');
                    $favs = $user->getFavs();
                    foreach($favs as $f){
                        $name = $f['name'];
                        $type = $f["type"];
                        echo("<tr><td><a href='search.php?bus=$name'>$name</a></td><td>$type</td></tr>");
                    }
                ?>
            </table>
        </div>
    </div>
</body>
</html>