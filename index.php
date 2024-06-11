<!DOCTYPE html>
<html>
<?php include "php/Bus.php"; ?>
<head>
    <title>Bus tracker</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
    <form id="form" method="post" action="php/search.php">
        <select id="bus" name="bus">
            <?php foreach (Bus::getUniqueActive() as $k => $v) {
                echo "<option>" . $k . "</option>";
            } ?>
        </select>
        <input type="submit" value="submit">
    </form>
    <h1>Enter a line number above to see their live positions</h1>
    </div>
</body>

</html>
