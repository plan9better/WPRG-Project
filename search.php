<!DOCTYPE html>
<?php include "Bus.php"; ?>
<html>
<head>
    <meta charset="UTF-8">
    <!-- TomTom Maps API -->
    <meta name='viewport'
          content='width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no'/>
    <link rel='stylesheet' type='text/css' href='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.1/maps/maps.css'>
    <!-- <link rel='stylesheet' type='text/css' href='../assets/ui-library/index.css'/> -->

    <script src="static/js/script.js"></script>
    <link rel="stylesheet" href="static/css/search_wtf.css">
    <link rel="stylesheet" href="static/css/nav.css">
    <title>Bus tracker</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
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
            <div id='map' class='map'></div>
    <script src='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.1/maps/maps-web.min.js'></script>
    <script type='text/javascript' src='mobile-or-tablet.js'></script>
    <script>
        API_KEY='azN4CwJpbcnMQr89Smid2eTMJkqRgcf7'
        const gdansk = [18.638306, 54.372158];
        var map = tt.map({
            key: API_KEY,
            container: 'map',
            dragPan: !isMobileOrTablet(),
            center: gdansk,
            zoom: 10,
        });
        map.addControl(new tt.FullscreenControl());
        map.addControl(new tt.NavigationControl());
        const marker = new tt.Marker().setLngLat(gdansk).addTo(map);
        var popupOffsets = {
        //   top: [0, 0],
          bottom: [0, -40],
        //   "bottom-right": [0, 70],
        //   "bottom-left": [0, 70],
        //   left: [10, 10],
        //   right: [-25, -35],
        }

        var popup = new tt.Popup({ offset: popupOffsets }).setHTML(
          "your company name, your company address"
        )
        marker.setPopup(popup).togglePopup()
    </script>
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
// const bus = JSON.parse(localStorage.getItem('key'));

// function loadMap(busData) {
//     // Initialize TomTom map
//     const map = tt.map({
//         key: API_KEY,
//         container: 'map',
//         center: [busData[0].lon, busData[0].lat], // Center on first bus
//         zoom: 12
//     });

//     // Add markers for each bus
//     busData.forEach(vehicle => {
//         const marker = new tt.Marker()
//             .setLngLat([vehicle.lon, vehicle.lat])
//             .addTo(map);

//         // Add popup with bus info
//         const popup = new tt.Popup({offset: 30})
//             .setHTML(`
//                 <strong>Route: ${vehicle.routeShortName}</strong><br>
//                 Direction: ${vehicle.headsign}<br>
//                 Speed: ${vehicle.speed} km/h
//             `);

//         marker.setPopup(popup);
//     });
// }

// loadMap(bus);
</script>
</body>
</html>