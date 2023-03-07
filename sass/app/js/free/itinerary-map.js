/**
 * @license
 * Copyright 2019 Google LLC. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0
 */
function initMap() {
  const directionsService = new google.maps.DirectionsService();
  const directionsRenderer = new google.maps.DirectionsRenderer();
  const map = new google.maps.Map(document.getElementById("tf-map"), {
    zoom: 15,
    center: { lat: 41.85, lng: -87.65 },
  });

  directionsRenderer.setMap(map);
  calculateAndDisplayRoute(directionsService, directionsRenderer);

}

function calculateAndDisplayRoute(directionsService, directionsRenderer) {
  const waypts = [];
  var locations = document.getElementById('tf-map').getAttribute('data-locations');
  locations = JSON.parse(locations);
  
  for (let i = 0; i < locations.length; i++) {
      waypts.push({
        location: locations[i],
        stopover: true,
      });
  }

  directionsService
    .route({
      origin: locations[0],
      destination: locations[locations.length - 1],
      waypoints: waypts,
      optimizeWaypoints: true,
      travelMode: google.maps.TravelMode.DRIVING,
    })
    // .then((response) => {
    //   directionsRenderer.setDirections(response);

    //   const route = response.routes[0];
    //   const summaryPanel = document.getElementById("directions-panel");

    //   summaryPanel.innerHTML = "";

    //   // For each route, display summary information.
    //   for (let i = 0; i < route.legs.length; i++) {
    //     const routeSegment = i + 1;

    //     summaryPanel.innerHTML +=
    //       "<b>Route Segment: " + routeSegment + "</b><br>";
    //     summaryPanel.innerHTML += route.legs[i].start_address + " to ";
    //     summaryPanel.innerHTML += route.legs[i].end_address + "<br>";
    //     summaryPanel.innerHTML += route.legs[i].distance.text + "<br><br>";
    //   }
    // })
}

window.onload = initMap;
