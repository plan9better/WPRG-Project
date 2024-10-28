<!DOCTYPE html>
<html>
<?php include "Bus.php"; ?>
<head>
    <title>Bus tracker</title>
    <link rel="stylesheet" href="static/css/index_wtf.css" />
    <link rel="stylesheet" href="static/css/nav.css">
</head>

<body>
    <?php include "nav.php"; ?>
    <div id="container">
    <form id="form" method="post" action="search.php">
        <select id="bus" name="bus">
            <?php foreach (Bus::getUniqueActive() as $k => $v) {
                echo "<option>" . $k . "</option>";
            } ?>
        </select>
        <input type="submit" value="submit">
    </form>
    <h1>Choose a line number above to see their live positions</h1>

    <div id="table">
        <h1>Every active vehicle at this time</h1>
        <table>
            <tr>
                <td>Route</td>
                <td>Headsign</td>
                <td>Patron</td>
                <td>Floor height</td>
                <td>Kneeling mechanism</td>
                <td>Wheelchairs ramp</td>
                <td>USB</td>
                <td>Bike Holders</td>
                <td>Ticket Machine</td>
                <td>Passenger doors</td>
                <td>Link</td>
            </tr>
                <?php if (isset($_GET["filter"])) {
                    foreach (Bus::getInfo() as $v) {
                        if ($v["routeShortName"] == $_GET["filter"]) {
                            echo "<tr>";
                            $route = $v["routeShortName"];
                            $headsign = $v["headsign"];
                            $patron =
                                $v["Patron"] == "brak" ? "None" : $v["Patron"];
                            $floor = $v["FloorHeight"];
                            $kneel =
                                $v["KneelingMechanism"] == 1 ? "Yes" : "No";
                            $ramp = $v["WheelchairsRamp"] == 1 ? "Yes" : "No";
                            $usb = $v["USB"] == 1 ? "Yes" : "No";
                            $bike = $v["BikeHolders"] == 1 ? "Yes" : "No";
                            $tickets = $v["TicketMachine"] == 1 ? "Yes" : "No";
                            $doors = $v["PassengersDoors"] == 1 ? "Yes" : "No";
                            $link = $v["URL"];
                            echo "<td><a href='search.php?bus=$route'>$route</a></td><td>$headsign</td><td>$patron</td><td>$floor</td><td>$kneel</td><td>$ramp</td><td>$usb</td><td>$bike</td><td>$tickets</td><td>$doors</td>";
                            echo "<td><a href='$link'>Info</a></td>";
                            echo "</tr>";
                        }
                    }
                } else {
                    foreach (Bus::getInfo() as $v) {
                        echo "<tr>";
                        $route = $v["routeShortName"];
                        $headsign = $v["headsign"];
                        $patron =
                            $v["Patron"] == "brak" ? "None" : $v["Patron"];
                        $floor = $v["FloorHeight"];
                        $kneel = $v["KneelingMechanism"] == 1 ? "Yes" : "No";
                        $ramp = $v["WheelchairsRamp"] == 1 ? "Yes" : "No";
                        $usb = $v["USB"] == 1 ? "Yes" : "No";
                        $bike = $v["BikeHolders"] == 1 ? "Yes" : "No";
                        $tickets = $v["TicketMachine"] == 1 ? "Yes" : "No";
                        $doors = $v["PassengersDoors"] == 1 ? "Yes" : "No";
                        $link = $v["URL"];
                        echo "<td><a href='search.php?bus=$route'>$route</a></td><td>$headsign</td><td>$patron</td><td>$floor</td><td>$kneel</td><td>$ramp</td><td>$usb</td><td>$bike</td><td>$tickets</td><td>$doors</td>";
                        echo "<td><a href='$link'>Info</a></td>";
                        echo "</tr>";
                    }
                } ?>
        </table>
    </div>
    </div>

</body>

</html>
