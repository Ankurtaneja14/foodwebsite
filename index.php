<!DOCTYPE html>
<html>

<head>
    <title>Progressive Form | Multi Steps Form</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v8.2.0/ol.css">

</head>

<body>

    <div class="wrapper">
        <div class="header">
            <ul>
                <li class="active form_1_progessbar">
                    <div>
                        <p>1</p>
                    </div>
                </li>
                <li class="form_2_progessbar">
                    <div>
                        <p>2</p>
                    </div>
                </li>
                <li class="form_3_progessbar">
                    <div>
                        <p>3</p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="form_wrap">
            <div class="form_1 data_info">
                <h1>Step 1: Image Upload</h1>
                <form id="uploadForm" action="upload.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="image" id="imageInput" accept="image/*" required>
                    <button type="button" onclick="uploadImage()">Upload Image</button>
                </form>
                <div id="imageContainer">
                    <h2>Uploaded Image:</h2>
                    <img id="uploadedImage" src="uploads/default.jpg" alt="Uploaded Image">
                </div>
            </div>
            <div class="form_2 data_info" style="display: none;">
                <h2>Add Address</h2>

                <form id="addressForm" onsubmit="submitForm(event)">
                    <label for="addressLine1">Address Line 1:</label>
                    <input type="text" id="addressLine1" required>

                    <label for="addressLine2">Address Line 2:</label>
                    <input type="text" id="addressLine2">

                    <label for="city">City:</label>
                    <input type="text" id="city" required>

                    <label for="state">State:</label>
                    <input type="text" id="state" required>

                    <label for="postalCode">Postal Code:</label>
                    <input type="text" id="postalCode" required>

                    <label for="country">Country:</label>
                    <input type="text" id="country" required>

                    <button type="button" onclick="openMap()">Select Location</button>
                    <button type="button" onclick="useCurrentLocation()">Use Current Location</button>

                    <div id="map"></div>
                    <button id="submit">Submit</button>
                    <div id="successMessage" style="visibility: hidden;">
                        <p>Address submitted successfully!</p>
                    </div>
                </form>
            </div>
            <div class="form_3 data_info" style="display: none;">
                <div id="map1"></div>
                <div id="buttons">
                    <button onclick="showCurrentLocation()">Current Location</button>
                    <button onclick="showKingStreetLocation()">King Street, Cambridge</button>
                </div>
            </div>
        </div>
        <div class="btns_wrap">
            <div class="common_btns form_1_btns">
                <button type="button" class="btn_next">Next <span class="icon"><ion-icon
                            name="arrow-forward-sharp"></ion-icon></span></button>
            </div>
            <div class="common_btns form_2_btns" style="display: none;">
                <button type="button" class="btn_back"><span class="icon"><ion-icon
                            name="arrow-back-sharp"></ion-icon></span>Back</button>
                <button type="button" class="btn_next">Next <span class="icon"><ion-icon
                            name="arrow-forward-sharp"></ion-icon></span></button>
            </div>
            <div class="common_btns form_3_btns" style="display: none;">
                <button type="button" class="btn_back"><span class="icon"><ion-icon
                            name="arrow-back-sharp"></ion-icon></span>Back</button>
                <button type="button" class="btn_done">Done</button>
            </div>
        </div>
    </div>

    <div class="modal_wrapper">
        <div class="shadow"></div>
        <div class="success_wrap">
            <span class="modal_icon"><ion-icon name="checkmark-sharp"></ion-icon></span>
            <p>You have successfully completed the process.</p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
  const map = new ol.Map({
    target: 'map1',  // Update the target to 'map1'
    layers: [
      new ol.layer.Tile({
        source: new ol.source.OSM(),
      }),
    ],
    view: new ol.View({
      center: ol.proj.fromLonLat([0, 0]),
      zoom: 2,
    }),
  });

  let currentLocationPin;
  let kingStreetPin;
  let lineFeature;

  function addRestaurantMarker(restaurant) {
    const marker = new ol.Feature({
      geometry: new ol.geom.Point(ol.proj.fromLonLat([restaurant.location.lon, restaurant.location.lat])),
      name: restaurant.name,
      address: restaurant.address || '',
    });

    const iconStyle = new ol.style.Style({
      image: new ol.style.Icon({
        anchor: [0.5, 1],
        src: 'https://openlayers.org/en/latest/examples/data/icon.png',
      }),
    });

    marker.setStyle(iconStyle);

    return marker;
  }

  function showRestaurantsOnMap(restaurants) {
    const restaurantLayer = new ol.layer.Vector({
      source: new ol.source.Vector({
        features: restaurants.map(addRestaurantMarker),
      }),
    });

    map.addLayer(restaurantLayer);
  }

  // Function to fetch restaurant data from JSON file
  async function fetchRestaurantData() {
    try {
      const response = await fetch('restaurants.json');
      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Error fetching restaurant data:', error);
      return [];
    }
  }

  // Show restaurants on the map after fetching data
  fetchRestaurantData().then(showRestaurantsOnMap);

  // Functions to handle button clicks
  window.showCurrentLocation = function () {
    navigator.geolocation.getCurrentPosition(function (position) {
      const currentLocation = ol.proj.fromLonLat([position.coords.longitude, position.coords.latitude]);
      if (currentLocationPin) {
        map.removeLayer(currentLocationPin);
      }
      currentLocationPin = new ol.layer.Vector({
        source: new ol.source.Vector({
          features: [new ol.Feature({
            geometry: new ol.geom.Point(currentLocation),
            name: 'Current Location',
          })],
        }),
      });
      map.addLayer(currentLocationPin);
      map.getView().animate({ center: currentLocation, zoom: 15 });
      drawLineToNearestRestaurant(currentLocation);
    });
  };

  window.showKingStreetLocation = function () {
    const kingStreetLocation = ol.proj.fromLonLat([0.1319, 52.2218]);
    if (kingStreetPin) {
      map.removeLayer(kingStreetPin);
    }
    kingStreetPin = new ol.layer.Vector({
      source: new ol.source.Vector({
        features: [new ol.Feature({
          geometry: new ol.geom.Point(kingStreetLocation),
          name: 'King Street, Cambridge',
        })],
      }),
    });
    map.addLayer(kingStreetPin);
    map.getView().animate({ center: kingStreetLocation, zoom: 15 });
    drawLineToNearestRestaurant(kingStreetLocation);
  };

  function drawLineToNearestRestaurant(sourceLocation) {
    const restaurants = map.getLayers().item(1).getSource().getFeatures();
    const nearestRestaurant = findNearestRestaurant(sourceLocation, restaurants);
    const destinationLocation = nearestRestaurant.getGeometry().getCoordinates();
    if (lineFeature) {
      map.removeLayer(lineFeature);
    }
    lineFeature = new ol.layer.Vector({
      source: new ol.source.Vector({
        features: [new ol.Feature({
          geometry: new ol.geom.LineString([sourceLocation, destinationLocation]),
        })],
      }),
      style: new ol.style.Style({
        stroke: new ol.style.Stroke({
          color: 'red',
          width: 3,
        }),
      }),
    });
    map.addLayer(lineFeature);

    alert('Food will be delivered from: ' + nearestRestaurant.get('name'));
  }

  function findNearestRestaurant(sourceLocation, restaurants) {
    let nearestDistance = Infinity;
    let nearestRestaurant = null;

    restaurants.forEach(function (restaurant) {
      const destinationLocation = restaurant.getGeometry().getCoordinates();
      const distance = ol.sphere.getDistance(sourceLocation, destinationLocation);
      if (distance < nearestDistance) {
        nearestDistance = distance;
        nearestRestaurant = restaurant;
      }
    });

    return nearestRestaurant;
  }
});

    </script>
    <script type="text/javascript" src="scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@v8.2.0/dist/ol.js"></script>

</body>

</html>