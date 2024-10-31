    API_KEY='azN4CwJpbcnMQr89Smid2eTMJkqRgcf7'
// function loadMap(buses){
//     const gdansk = [18.638306, 54.372158];
//     var map = tt.map({
//         key: API_KEY,
//         container: 'map',
//         dragPan: !isMobileOrTablet(),
//         center: gdansk,
//         zoom: 10,
//     });
//     const marker = new tt.Marker().setLngLat(gdansk).addTo(map);
//     var popupOffsets = {
//     //   top: [0, 0],
//       bottom: [0, -40],
//     //   "bottom-right": [0, 70],
//     //   "bottom-left": [0, 70],
//     //   left: [10, 10],
//     //   right: [-25, -35],
//     }

//     var popup = new tt.Popup({ offset: popupOffsets }).setHTML(
//       "your company name, your company address"
//     )
//     marker.setPopup(popup).togglePopup()
// }
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
  // setting map center
  let mapCenter = { lat: 0, lng: 0 };
    console.log(data)
  for (bus in data) {
    mapCenter["lat"] += data[bus]["position"]["lat"];
    mapCenter["lng"] += data[bus]["position"]["lng"];
  }
  mapCenter["lat"] /= data.length;
  mapCenter["lng"] /= data.length;
  mapCenter = [mapCenter['lng'], mapCenter['lat']];

  let map = tt.map({
    key: API_KEY,
    container: 'map',
    dragPan: !isMobileOrTablet(),
    zoom: 12,
    center: mapCenter,
  });
  map.addControl(new tt.FullscreenControl());
  map.addControl(new tt.NavigationControl());

  let markers = [];

  for (bus in data) {
    // var element = document.createElement("div")
    // element.id = "marker"
    let coords = [data[bus]["position"]['lng'],data[bus]["position"]['lat']]
    // var style = document.createElement('style');
    // const amount = data[bus]['direction'];
    // style.innerHTML = `.marker${bus}{ rotate(${amount}deg) }`;
    // element.appendChild(style)
    // document.getElementsByTagName('head')[0].appendChild(style);

    // element.className = `marker${bus}`;
    let points = [];

    points.push({
      coordinates: coords,
      properties: {
        id: bus,
        name: "bus" + bus
      }
      // new tt.Marker({element: element}).setLngLat(coords).addTo(map)
    });

    // let destLat, destLng;
    // destLat =
    //   0.00003 *
    //   data[bus]["position"]["lat"] *
    //   Math.cos(data[bus]["angle"] * (Math.PI / 180));
    // destLng =
    //   0.0003 *
    //   data[bus]["position"]["lng"] *
    //   Math.sin(data[bus]["angle"] * (Math.PI / 180));
    // destLat += data[bus]["position"]["lat"];
    // destLng += data[bus]["position"]["lng"];

    // arrows.push(
    //   new google.maps.Polyline({
    //     path: [data[bus]["position"], { lat: destLat, lng: destLng }],
    //     icons: [
    //       {
    //         icon: lineSymbol,
    //         offset: "100%",
    //       },
    //     ],
    //     map: map,
    //   }),
    // );
  }
}
