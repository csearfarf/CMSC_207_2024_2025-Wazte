<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats  pt-3 pb-3">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Total Facilities</h5><span
                class="h2 font-weight-bold mb-0">
                <?= esc($totalFacilities) ?>
              </span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                <i class="ni ni-pin-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats pt-3 pb-3">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Total Facilitators</h5><span
                class="h2 font-weight-bold mb-0">
                <?= esc($totalFacilitator) ?>
              </span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                <i class="ni ni-building"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats pt-3 pb-3">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Total Users</h5><span
                class="h2 font-weight-bold mb-0">
                <?= esc($totalUsers) ?>
              </span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                <i class="ni ni-compass-04"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats pt-3 pb-3">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Total Undecided Users</h5><span
                class="h2 font-weight-bold mb-0">
                <?= esc(data: $totalBlank) ?>
              </span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                <i class="ni ni-user-run"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-7">
      <div class="card ">
        <div class="card-header bg-transparent">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="text-muted text-uppercase ls-1 mb-1">Overview</h6>
              <h5 class="h3 mb-0">Registered Facilities</h5>
            </div>

            <div class="col text-right ">
              <a href="<?= base_url('admin/facility'); ?>" class=" btn btn-sm
                btn-primary">See all</a>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div id="map" class="map-canvas position-relative" style="height:500px;max-height:800px;"></div>
        </div>
      </div>
    </div>
    <div class="col-xl-5">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">Recent User Logs</h3>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table align-items-center table-flush" id="recentSessions">
            <thead class="thead-light">
              <tr>
                <th scope="col">Email</th>
                <th scope="col">Previous URL</th>
                <th scope="col">Last seen</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>


</div>



<script>
  function loadRecentSessions() {
    $.getJSON('<?= base_url("admin/dashboard/recent") ?>')
      .done(function (data) {
        const $tbody = $('#recentSessions tbody').empty();

        $.each(data, function (_, session) {
          const email = session.email || '';
          const last = session.last_login || '';

          // truncate URL to start at "/wazte", or leave it if not found
          const rawUrl = session.previous_url || '';
          const idx = rawUrl.indexOf('/wazte');
          const displayUrl = idx !== -1
            ? rawUrl.slice(idx)
            : rawUrl;

          $tbody.append(`
          <tr>
            <th scope="row">${email}</th>
            <td>${displayUrl}</td>
            <td>${last}</td>
          </tr>
        `);
        });
      })
      .fail(function (jqxhr, textStatus, error) {
        console.error('Failed to load recent sessions:', error);
      });
  }

  // call on page ready
  $(function () {
    loadRecentSessions();
  });
</script>
<script>
  // Globals
  let map, infoWindow;
  let directionsService, directionsRenderer;

  /**
   * Google's callback for script loading.
   * First tries to get browser geolocation, then initializes the map.
   */
  window.initMap = () => {
    // 1) get user location (or fallback) then init
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        ({ coords }) => initializeMap(coords.latitude, coords.longitude),
        () => initializeMap() // fallback
      );
    } else {
      initializeMap(); // no geolocation support
    }
  };

  /**
   * Initialize the Google map with given or default coords.
   */
  function initializeMap(lat = 14.5995, lng = 120.9842) {
    map = new google.maps.Map(document.getElementById("map"), {
      center: { lat, lng },
      zoom: 12,
      disableDefaultUI: true,
      mapTypeControl: false,
      fullscreenControl: false,
      styles: [
        { featureType: "poi", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
        { featureType: "poi.business", stylers: [{ visibility: "off" }] },
        { featureType: "transit", stylers: [{ visibility: "off" }] },
        { featureType: "administrative", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
      ]
    });

    // 3) prepare a single InfoWindow and Directions objects
    infoWindow = new google.maps.InfoWindow();
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
      suppressMarkers: false,      // show default markers for route endpoints
      polylineOptions: {           // style of the route line
        strokeColor: '#4285F4',
        strokeWeight: 5
      }                            // <-- use } here, not ]
    });
    // start with no map attached; will attach when route is drawn
    directionsRenderer.setMap(null);

    // clear route when InfoWindow closes
    google.maps.event.addListener(infoWindow, 'closeclick', () => {
      directionsRenderer.setMap(null);
    });

    loadFacilityMarkers();
  }


  /**
   * Fetches facility list and drops markers + infowindows.
   */
  function loadFacilityMarkers() {
    axios.get('<?= base_url("facility/list") ?>')
      .then(({ data }) => {
        data.forEach(f => {
          const pos = {
            lat: parseFloat(f.location.lat),
            lng: parseFloat(f.location.lng)
          };
          const marker = new google.maps.Marker({ position: pos, map });

          // build your info-window HTML, with a "Navigate" button
          const badges = f.tags.map(t =>
            `<span class="badge bg-primary me-1 text-white">${t.Material || t.name}</span>`
          ).join('');
          const content = `
            <div style="min-width:220px">
              <h3>${f.name}</h3>
              <p class="m-0">${f.Description}</p>
              <p class="m-0"><strong>Hours:</strong> ${f.BusinessHours}</p>
              <p>${badges}</p>
              <button id="nav-btn" class="btn btn-sm btn-success mt-2">
                Navigate
              </button>
            </div>`;

          marker.addListener('click', () => {
            infoWindow.setContent(content);
            infoWindow.open(map, marker);

            // once the DOM is in the page, hook the Navigate button
            google.maps.event.addListenerOnce(infoWindow, 'domready', () => {
              document.getElementById('nav-btn').addEventListener('click', () => {
                // get current location again (in case user moved)
                if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(
                    ({ coords }) => {
                      // draw route from current pos → marker pos
                      directionsService.route({
                        origin: { lat: coords.latitude, lng: coords.longitude },
                        destination: pos,
                        travelMode: 'DRIVING'
                      }, (result, status) => {
                        if (status === 'OK') {
                          directionsRenderer.setDirections(result);
                          directionsRenderer.setMap(map);
                        } else {
                          alert('Directions request failed due to ' + status);
                        }
                      });
                    },
                    () => alert('Unable to retrieve your location')
                  );
                } else {
                  alert('Geolocation not supported by your browser');
                }
              });
            });
          });
        });
      })
      .catch(err => console.error("Couldn't load facilities:", err));
  }

</script>