<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Wazte</title>

  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

  <!-- Select2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <!-- Axios -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <style>
    /* Global Resets */
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Roboto', sans-serif;
    }
    body {
      display: flex;
      flex-direction: column;
    }
    /* Navbar */
    .navbar {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      z-index: 1000;
    }
    .navbar-brand {
      font-weight: 600;
    }
    /* Main Container: Sidebar + Map */
    #main-container {
      flex: 1;
      display: flex;
      overflow: hidden;
    }
    /* Sidebar */
    .sidebar {
      width: 380px;
      background: #fff;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      padding: 20px;
    }
    #places-list {
      flex-grow: 1;
      overflow-y: auto;
      margin-top: 10px;
    }
    /* Map occupies remaining space */
    #map {
      flex: 1;
      position: relative;
    }
    /* Search Bar */
    .search-bar {
      display: flex;
      align-items: center;
      background: #fff;
      border-radius: 50px;
      padding: 6px 14px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      max-height: 40px;
    }
    .search-bar input {
      border: none;
      outline: none;
      width: 300px !important;
      font-size: 14px;
      max-height: 28px;
      flex: 1;
    }
    /* Category Buttons */
    .category-scroll {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }
    .category-button {
      display: flex;
      align-items: center;
      gap: 6px;
      border: 1px solid #ccc;
      background: #fff;
      border-radius: 30px;
      padding: 6px 12px;
      font-size: 14px;
      white-space: nowrap;
      box-shadow: 0 1px 2px rgba(0,0,0,0.08);
      flex: 1 1 auto;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .category-button:hover {
      background-color: #f5f5f5;
    }
    .category-button.active {
      background-color: #007bff;
      color: #fff;
      border-color: #007bff;
    }
    .place-result:hover {
      background-color: #f5f5f5;
    }
    ::-webkit-scrollbar-button {
      display: none;
    }
    @media (max-width: 768px) {
      #main-container {
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        height: 50%;
      }
      #map {
        width: 100%;
        height: 50%;
      }
      #navbarContent #filtersNav {
        flex-direction: column;
        gap: 14px;
      }
      .search-bar input {
        width: 300px;
      }
    }
    @media (max-width: 1400px) {
      .search-bar {
        width: 100% !important;
      }
      #navIcon {
        flex-direction: column !important;
      }
      body > nav > div > div.d-flex.flex-row {
        width: 100% !important;
      }
    }
    #navToggler {
      height: 40px;
    }
    .navbar-collapse.collapsing {
      transition: height 0.5s ease !important;
    }
    .modal {
      z-index:2000;
    }
    .select2-dropdown.select2--small {
      z-index: 999999;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-light p-2">
  <div class="container-fluid d-flex gap-3" id="navIcon">
    <div class="d-flex flex-row">
      <a class="navbar-brand p-0 me-auto" href="#">
        <img src="<?= base_url('public/wazte_logo.png') ?>" alt="Wazte" width="115" class="d-inline-block align-text-top">
      </a>
      <button class="navbar-toggler mt-2" type="button" id="navToggler" data-bs-toggle="collapse" 
              data-bs-target="#navbarContent" aria-controls="navbarContent" 
              aria-expanded="false" aria-label="Toggle navigation">
        <span><i class="fas fa-bars"></i></span>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="navbarContent">
      <div class="d-flex gap-3 flex-wrap" id="filtersNav">
        <div class="search-bar">
          <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            <i class="fa-solid fa-sliders"></i>
          </button>
          <input type="text" placeholder="Search e-waste facility ..." class="w-100" />
          <i class="fas fa-search text-muted flex-shrink-1"></i>
        </div>
        <div class="d-flex flex-wrap gap-2 category-scroll" id="material-buttons"></div>
      </div>
    </div>
  </div>
</nav>

<!-- MAIN CONTAINER -->
<div id="main-container">
  <div class="sidebar">
    <div class="d-flex align-items-center">
      <div class="me-auto">
        <h4 class="fw-bold mb-0">Facilities</h4>
      </div>
    </div>
    <hr>
    <div id="places-list" class="flex-grow-1 overflow-auto mt-2"></div>
  </div>
  <div id="map"></div>
</div>

<!-- Modal for Map Filters -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Configure Map Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3 w-100">
          <select class="form-select" data-placeholder="Choose filters" id="small-select2-options-multiple-field" multiple>
            <option>Google Places</option>
            <option>Wazte Places</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Understood</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (required for select2) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
<!-- Google Maps -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= getenv('GOOGLE_MAPS_API_KEY') ?>&libraries=places&callback=initMap"></script>

<script>
  let map;
  let activeInfoWindow = null;
  let currentUserLocation = null;
  let directionsRenderer, directionsService;
  const markers = {};
  const placeDetailsMap = {};
  // Active material filter set via category buttons.
  let activeMaterialFilter = null;
  // Combined fetched results.
  let latestResults = [];
  // Material categories loaded from your backend.
  let materialCategories = [];

  // Helper: For Google Places, add extra type badges.
  function getGoogleTypeBadges(place) {
    let badges = "";
    if (place.types) {
      // Common generic types to ignore.
      const ignore = ["point_of_interest", "establishment", "premise", "locality"];
      place.types.forEach(t => {
        if (!ignore.includes(t)) {
          badges += `<span class="badge bg-secondary me-1">${t}</span>`;
        }
      });
    }
    return badges;
  }

  // Helper: Display the full list of Google place types (comma separated).
  function displayGooglePlaceTypes(place) {
    if (!place.types) return "";
    const ignore = ["point_of_interest", "establishment", "premise", "locality"];
    const validTypes = place.types.filter(t => !ignore.includes(t));
    return validTypes.length ? `<div><small>Place Types: ${validTypes.join(", ")}</small></div>` : "";
  }

  function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
      center: { lat: 14.5995, lng: 120.9842 },
      zoom: 14,
      disableDefaultUI: false,
      mapTypeControl: false,
      fullscreenControl: false,
      styles: [
        { featureType: "poi", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
        { featureType: "poi.business", stylers: [{ visibility: "off" }] },
        { featureType: "transit", stylers: [{ visibility: "off" }] },
        { featureType: "administrative", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
      ]
    });

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(position => {
        currentUserLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        map.setCenter(currentUserLocation);
        new google.maps.Marker({
          position: currentUserLocation,
          map: map,
          title: 'You are here'
        });
        reloadPlaces();
      });
    } else {
      currentUserLocation = { lat: 14.5995, lng: 120.9842 };
      map.setCenter(currentUserLocation);
      reloadPlaces();
    }
    loadMaterialTypes();
  }

  // Reload places based on selected filters and active material filter.
  function reloadPlaces() {
    if (!currentUserLocation) return;
    clearMarkers();
    let allResults = [];
    let pending = 0;
    const selectedFilters = $('#small-select2-options-multiple-field').val() || [];

    if (selectedFilters.includes("Google Places")) {
      pending++;
      loadGooglePlaces(currentUserLocation, function(results) {
        allResults = allResults.concat(results);
        pending--;

        console.log(allResults)
        if (pending === 0) processResults(allResults);
      });
    }
    if (selectedFilters.includes("Wazte Places")) {
      pending++;
      loadWaztePlaces(currentUserLocation, function(results) {
        allResults = allResults.concat(results);
        pending--;
        if (pending === 0) processResults(allResults);
      });
    }
    if (pending === 0) processResults(allResults);
  }

  function loadGooglePlaces(userLocation, callback) {
    const service = new google.maps.places.PlacesService(map);
    service.nearbySearch({
      location: userLocation,
      radius: 5000,
      keyword: "recycling center"
    }, (results, status) => {
      if (status === google.maps.places.PlacesServiceStatus.OK && results) {
        callback(results);
      } else {
        console.error("Google Places API error:", status);
        callback([]);
      }
    });
  }

  // Simulated load for Wazte Places (replace with backend API call later).
  function loadWaztePlaces(userLocation, callback) {
    let sampleResults = [
      {
        place_id: "wazte1",
        name: "Wazte Recycling Facility 1",
        geometry: { location: new google.maps.LatLng(userLocation.lat + 0.01, userLocation.lng + 0.01) },
        vicinity: "123 Wazte St.",
        types: ["recycling", "plastics", "e-waste"]
      },
      {
        place_id: "wazte2",
        name: "Wazte Recycling Facility 2",
        geometry: { location: new google.maps.LatLng(userLocation.lat - 0.01, userLocation.lng - 0.01) },
        vicinity: "456 Wazte Ave.",
        types: ["recycling", "metals"]
      }
    ];
    setTimeout(function() { callback(sampleResults); }, 500);
  }

  // Process and filter results.
  function processResults(allResults) {
    latestResults = allResults.filter((place, index, self) =>
      index === self.findIndex(p => p.place_id === place.place_id)
    );
    if (activeMaterialFilter) {
      latestResults = latestResults.filter(place => {
        if (place.types && place.types.length > 0) {
          return place.types.some(type => type.toLowerCase().includes(activeMaterialFilter.toLowerCase()));
        }
        return false;
      });
    }
    console.table(latestResults);
    renderMarkers(latestResults);
    renderPlacesList(latestResults);
  }

  function clearMarkers() {
    for (const key in markers) {
      markers[key].setMap(null);
      delete markers[key];
    }
    document.getElementById('places-list').innerHTML = '';
  }

  // Render markers with InfoWindows that include source, material category badges, extra Google type badges, and full Google place types.
  function renderMarkers(places) {
    console.log(places)
    places.forEach(place => {
      const marker = new google.maps.Marker({
        map: map,
        position: place.geometry.location,
        title: place.name,
        icon: {
          url: 'http://localhost:8756/207/project_code/wazte/public/nature.svg',
          scaledSize: new google.maps.Size(30, 30)
        }
      });
      markers[place.place_id] = marker;
      
      // Source badge.
      const sourceBadge = (place.place_id && place.place_id.startsWith("wazte"))
        ? '<span class="badge bg-success me-1">Wazte</span>'
        : '<span class="badge bg-info me-1">Google</span>';
      
      // Material category badges.
      let materialBadges = "";
      if (place.types && materialCategories.length > 0) {
        materialCategories.forEach(cat => {
          if (place.types.some(t => t.toLowerCase().includes(cat.name.toLowerCase()))) {
            materialBadges += `<span class="badge bg-info me-1">${cat.name}</span>`;
          }
        });
      }
      
      // Extra badges for Google Places.
      let googleBadges = "";
      let googleTypesText = "";
      if (!(place.place_id && place.place_id.startsWith("wazte"))) {
        googleBadges = getGoogleTypeBadges(place);
        googleTypesText = displayGooglePlaceTypes(place);
      }
      
      const infoWindowContent = `
        <strong class="fw-bold">${place.name}</strong> ${sourceBadge} ${materialBadges} ${googleBadges} ${googleTypesText}<br>
        ${place.vicinity || place.formatted_address || 'No address available'}<br>
        ${place.rating ? '⭐ ' + place.rating : ''}<br>
        <button class="btn btn-sm btn-primary mt-2" onclick="navigateToPlace('${place.place_id}')">Navigate</button>
      `;
      const infoWindow = new google.maps.InfoWindow({ content: infoWindowContent });
      google.maps.event.addListener(infoWindow, 'domready', () => {
        const iwCloseBtn = document.querySelector('button[title="Close"]');
        if (iwCloseBtn) { iwCloseBtn.blur(); }
      });
      google.maps.event.addListener(infoWindow, 'closeclick', () => {
        if (directionsRenderer) { directionsRenderer.set('directions', null); }
        if (currentUserLocation) { map.setCenter(currentUserLocation); map.setZoom(14); }
      });
      marker.addListener("click", () => {
        if (activeInfoWindow) {
          activeInfoWindow.close();
          if (directionsRenderer) { directionsRenderer.set('directions', null); }
        }
        let extraDetails = '';
        const details = placeDetailsMap[place.place_id];
        if (details) {
          if (details.opening_hours && typeof details.opening_hours.isOpen === 'function') {
            const isOpen = details.opening_hours.isOpen();
            extraDetails += `<br>Status: ${isOpen ? '<span style="color:green;">Open Now</span>' : '<span style="color:red;">Closed</span>'}`;
            if (details.opening_hours.weekday_text) {
              extraDetails += `<br>Office Hours:<br>${details.opening_hours.weekday_text.join('<br>')}`;
            }
          }
          if (details.formatted_phone_number) {
            extraDetails += `<br>Contact: ${details.formatted_phone_number}`;
          }
        } else {
          extraDetails = `<br>Loading details...`;
        }
        const updatedContent = `
          <strong>${place.name}</strong> ${sourceBadge} ${materialBadges} ${googleBadges} ${googleTypesText}<br>
          ${place.vicinity || place.formatted_address || 'No address available'}<br>
          ${place.rating ? '⭐ ' + place.rating : ''}${extraDetails}<br>
          <button class="btn btn-sm btn-primary mt-2" onclick="navigateToPlace('${place.place_id}')">Navigate</button>
        `;
        infoWindow.setContent(updatedContent);
        infoWindow.open(map, marker);
        activeInfoWindow = infoWindow;
      });
    });
  }

  // Render the sidebar list with similar details.
  function renderPlacesList(places) {
    const container = document.getElementById('places-list');
    container.innerHTML = '';
    places.forEach(place => {
      const isWazte = place.place_id && place.place_id.startsWith("wazte");
      const sourceBadge = isWazte
        ? '<span class="badge bg-success me-1">Wazte</span>'
        : '<span class="badge bg-info me-1">Google</span>';
      let materialBadges = "";
      if (place.types && materialCategories.length > 0) {
        materialCategories.forEach(cat => {
          if (place.types.some(t => t.toLowerCase().includes(cat.name.toLowerCase()))) {
            materialBadges += `<span class="badge bg-info me-1">${cat.name}</span>`;
          }
        });
      }
      let googleBadges = "";
      let googleTypesText = "";
      if (!isWazte) {
        googleBadges = getGoogleTypeBadges(place);
        googleTypesText = displayGooglePlaceTypes(place);
      }
      const div = document.createElement('div');
      div.className = 'place-result p-2 mb-2 border rounded me-2';
      div.style.cursor = 'pointer';
      div.innerHTML = `
        ${sourceBadge} ${materialBadges} ${googleBadges} ${googleTypesText}
        <span class="mb-1 fw-bold">${place.name}</span><br>
        <small>${place.vicinity || place.formatted_address || ''}</small>
      `;
      div.addEventListener('click', () => {
        const marker = markers[place.place_id];
        if (marker) {
          map.setCenter(marker.getPosition());
          map.setZoom(15);
          google.maps.event.trigger(marker, 'click');
        }
      });
      container.appendChild(div);
      getPlaceDetails(place.place_id, function(details) {
        placeDetailsMap[place.place_id] = details;
      });
    });
  }

  function getPlaceDetails(placeId, callback) {
    const service = new google.maps.places.PlacesService(map);
    service.getDetails({
      placeId: placeId,
      fields: ['name', 'rating', 'formatted_address', 'vicinity', 'opening_hours', 'formatted_phone_number', 'website','types','category']
    }, (details, status) => {
      if (status === google.maps.places.PlacesServiceStatus.OK) {
        console.log(details)
        callback(details);
      } else {
        console.error(`Details request failed for placeId ${placeId}:`, status);
        callback({});
      }
    });
  }

  function navigateToPlace(placeId) {
    const marker = markers[placeId];
    if (!marker) return;
    if (!currentUserLocation) {
      alert("User location not available");
      return;
    }
    if (!directionsService) { directionsService = new google.maps.DirectionsService(); }
    if (!directionsRenderer) {
      directionsRenderer = new google.maps.DirectionsRenderer();
      directionsRenderer.setMap(map);
    }
    directionsService.route({
      origin: currentUserLocation,
      destination: marker.getPosition(),
      travelMode: google.maps.TravelMode.DRIVING
    }, (response, status) => {
      if (status === google.maps.DirectionsStatus.OK) {
        directionsRenderer.setDirections(response);
      } else {
        alert("Could not display directions due to: " + status);
      }
    });
  }

  // Load material types from your backend.
  function loadMaterialTypes() {
    axios.get('<?= base_url("home/materialTypes") ?>')
      .then(response => {
        materialCategories = response.data; // Store globally.
        const container = document.getElementById('material-buttons');
        container.innerHTML = '';
        response.data.forEach(material => {
          const button = document.createElement('div');
          button.className = 'category-button';
          button.innerHTML = `<i class="fas ${material.icon}"></i> ${material.name}`;
          button.addEventListener('click', () => {
            if (activeMaterialFilter === material.name) {
              activeMaterialFilter = null;
              button.classList.remove('active');
            } else {
              activeMaterialFilter = material.name;
              document.querySelectorAll('.category-button').forEach(btn => btn.classList.remove('active'));
              button.classList.add('active');
            }
            reloadPlaces();
          });
          container.appendChild(button);
        });
      })
      .catch(err => {
        console.error("Failed to load material types:", err);
      });
  }

  window.initMap = initMap;

  $(document).ready(function() {
    $('#small-select2-options-multiple-field').select2({
      theme: "bootstrap-5",
      width: '100%',
      placeholder: "Choose filters",
      closeOnSelect: false,
      selectionCssClass: 'select2--small',
      dropdownCssClass: 'select2--small'
    });
    $('#small-select2-options-multiple-field').val(["Google Places", "Wazte Places"]).trigger('change');
    $('#small-select2-options-multiple-field').on('change', function() {
      reloadPlaces();
    });
  });
</script>
</body>
</html>
