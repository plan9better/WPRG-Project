<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
    <link rel="stylesheet" href="static/css/account_wtff.css">
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
    <?php if (isset($_POST["logout"])) {
        unset($_POST["logout"]);
        setcookie("bussession", "", -1, "/");
        echo '<h1 id="success">Success. Go home to see results.</h1>';

    }
    if(isset($_POST["add"])){
        $user = new User($_COOKIE["bussession"]);
        $favs = $user->getFavs();
        $add = true;
        foreach ($favs as $value) {
            if($value["name"] == $_POST["bus"]){
                $add = false;
                break;
            }
        }
        if($add){
            $user->addFav($_POST["bus"]);
        } else {
            echo("<h1 id='error'>That entry already exists</h1>");
        }
    }
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
                <input type="submit" name="add" value="add">
            </form>
            <table>
                <tr>
                    <td>Name</td>
                    <td>Type</td>
                    <td>Active</td>
                </tr>
                <?php
                    $user = new User($_COOKIE["bussession"]);
                    $favs = $user->fetchFavs();
                    $active = Bus::getUniqueActive();

                    foreach($favs as $f){
                        $name = $f['name'];
                        $type = $f["type"];

                        if(in_array($name, array_keys($active))){
                            echo("<tr><td><a href='search.php?bus=$name'>$name</a></td><td>$type</td><td>Yes</td></tr>");
                        }else{
                            echo("<tr><td>$name</td><td>$type</td><td>No</td></tr>");

                        }
                        // $isActive = (in_array($name, $active)) ? "Yes" : "No";
                        // echo("<tr><td><a href='search.php?bus=$name'>$name</a></td><td>$type</td><td>$isActive</td></tr>");
                    }
                ?>
            </table>
        </div>
    </div>
</body>
</html>