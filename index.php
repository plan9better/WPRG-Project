<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Bus tracker</title>
    </head>
    <body>
        <nav>
            <a href="index.php">Home</a>

            <!-- Redirect to /login when not logged in -->
            <a href="search.php"> Search </a>
            <a href="favorites.php">My favorites</a>

            <!-- Change when logged in -->
            <a href="login.php">Login</a>
        </nav>
        <form id="busForm" method="post" action="index.php">
            <input type="text" id="bus">
            <input type="submit" name="submit" value="search">
        </form>
        <div id="main">
<?php if (isset($_POST["bus"])) {
    echo `<div id="map"></div><div id="noBuses></div>`;
} else {
    echo `<h1>Please enter a route id to get started</h1>`;
} ?>
        </div>


        <!-- prettier-ignore -->
        <script>(g => { var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document, b = window; b = b[c] || (b[c] = {}); var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams, u = () => h || (h = new Promise(async (f, n) => { await (a = m.createElement("script")); e.set("libraries", [...r] + ""); for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]); e.set("callback", c + ".maps." + q); a.src = `https://maps.${c}apis.com/maps/api/js?` + e; d[q] = f; a.onerror = () => h = n(Error(p + " could not load.")); a.nonce = m.querySelector("script[nonce]")?.nonce || ""; m.head.append(a) })); d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n)) })({ key: API_KEY, v: "weekly" });</script>
        <script src="https://unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
        <script src="script.js"></script>
    </body>
</html>
