function loadMap(buses) {
  let data = [];
  for (bus in buses) {
    data.push({
      position: {
        lat: parseFloat(buses[bus]["lat"]),
        lng: parseFloat(buses[bus]["lon"]),
      },
      tripId: buses[bus]["tripId"],
      angle: buses[bus]["direction"],
      headsign: buses[bus]["headsign"],
    });
  }
  initMap(data);
}

async function initMap(data) {
  let map = document.getElementById("map");
  map.innerHTML = "";
  // Request needed libraries.
  const { Map } = await google.maps.importLibrary("maps");
  const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

  let mapCenter = { lat: 0, lng: 0 };
  for (bus in data) {
    mapCenter["lat"] += data[bus]["position"]["lat"];
    mapCenter["lng"] += data[bus]["position"]["lng"];
  }
  mapCenter["lat"] /= data.length;
  mapCenter["lng"] /= data.length;

  map = new Map(document.getElementById("map"), {
    zoom: 14,
    center: mapCenter,
    mapId: "demo",
  });

  let markers = [];
  let arrows = [];
  const lineSymbol = {
    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
  };

  for (bus in data) {
    markers.push(
      new AdvancedMarkerElement({
        map: map,
        position: data[bus]["position"],
        title: data[bus]["headsign"],
      }),
    );

    let destLat, destLng;
    destLat =
      0.00003 *
      data[bus]["position"]["lat"] *
      Math.cos(data[bus]["angle"] * (Math.PI / 180));
    destLng =
      0.0003 *
      data[bus]["position"]["lng"] *
      Math.sin(data[bus]["angle"] * (Math.PI / 180));
    destLat += data[bus]["position"]["lat"];
    destLng += data[bus]["position"]["lng"];

    arrows.push(
      new google.maps.Polyline({
        path: [data[bus]["position"], { lat: destLat, lng: destLng }],
        icons: [
          {
            icon: lineSymbol,
            offset: "100%",
          },
        ],
        map: map,
      }),
    );
  }
}
