<!DOCTYPE html>
<?php include "/var/www/html/project/Bus.php"; ?>
<html>
<head>
    <meta charset="UTF-8">

    <!-- MAPS API KEY -->
    <script src="secret/API_KEY.js"></script>
    <!-- MAPS API -->
    <script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })
    ({ key: API_KEY, v: "weekly" });</script>

    <script src="static/js/script.js"></script>
    <link rel="stylesheet" href="static/css/search_wtf.css">
    <link rel="stylesheet" href="static/css/nav.css">
    <title>Bus tracker</title>
</head>
<body>

    <?php
    include "nav.php";
    if (!isset($_REQUEST["bus"])) {
        echo "<h1>Something went wrong... Please go to home and try again.</h1>";
        exit();
    }
    ?>
    <div id="content">
        <h1>Map of active vehicles on route <?php echo htmlentities(
            $_REQUEST["bus"]
        ); ?></h1>
        <div id="map"></div>
    </div>

<?php
$bus = Bus::getVehiclesByRoute(htmlentities($_REQUEST["bus"]));
while (empty($bus)) {
    sleep(0.2);
    $bus = Bus::getVehiclesByRoute(htmlentities($_REQUEST["bus"]));
}
$enc = json_encode($bus);
echo "<script>localStorage.setItem('key', '$enc');</script>";
?>

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
            <?php foreach (Bus::getInfo() as $v) {
                if ($v["routeShortName"] == $_REQUEST["bus"]) {
                    echo "<tr>";
                    $route = $v["routeShortName"];
                    $headsign = $v["headsign"];
                    $patron = $v["Patron"] == "brak" ? "None" : $v["Patron"];
                    $floor = $v["FloorHeight"];
                    $kneel = $v["KneelingMechanism"] == 1 ? "Yes" : "No";
                    $ramp = $v["WheelchairsRamp"] == 1 ? "Yes" : "No";
                    $usb = $v["USB"] == 1 ? "Yes" : "No";
                    $bike = $v["BikeHolders"] == 1 ? "Yes" : "No";
                    $tickets = $v["TicketMachine"] == 1 ? "Yes" : "No";
                    $doors = $v["PassengersDoors"] == 1 ? "Yes" : "No";
                    $link = $v["URL"];
                    echo "<td>$route</td><td>$headsign</td><td>$patron</td><td>$floor</td><td>$kneel</td><td>$ramp</td><td>$usb</td><td>$bike</td><td>$tickets</td><td>$doors</td>";
                    echo "<td><a href='$link'>Info</a></td>";
                    echo "</tr>";
                }
            } ?>
    </table>
</div>
<script type="text/javascript">
 bus = JSON.parse(localStorage.getItem('key'));
 loadMap(bus);

</script>

</body>
</html>
