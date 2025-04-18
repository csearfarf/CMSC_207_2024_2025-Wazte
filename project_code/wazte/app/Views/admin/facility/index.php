<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col-xl-4">
      <div class="card">
        <div class="d-flex flex-row-reverse">
          <div class="p-3">
            <button onclick="openCreateNew()" class="btn btn-primary">Create new facility</button>
          </div>
        </div>
        <div class="card-body table-responsive">
          <table id="usersTable" class="table align-items-center">
            <thead class="thead-light">
              <tr>
                <th>Name</th>
                <th class="text-right">Option</th>
              </tr>
            </thead>
            <tbody class="list">
              <!-- … -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-xl-8">
      <div class="card">
        <div class="card-body p-0">
          <div id="map" class="map-canvas position-relative"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create New Facility</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <!-- Facility Name -->
            <div class="form-group mb-3">
              <label for="addName">Facility Name</label>
              <input id="addName" type="text" class="form-control" placeholder="Facility Name">
            </div>
            <hr>
            <!-- Pin button -->
            <div class="mb-3">
              <button type="button" class="btn btn-secondary w-100" onclick="selectonMap()">
                Pin Location
              </button>
            </div>
            <!-- Lat / Lng -->
            <div class="form-group mb-3 row">
              <div class="col-12">
                <label>Facility Address</label>
                <input id="addAddr" type="text" class="form-control" disabled>
              </div>

              <div class="col" hidden>
                <label>Lat</label>
                <input id="addLat" type="text" class="form-control" disabled>
              </div>
              <div class="col" hidden>
                <label>Lng</label>
                <input id="addLng" type="text" class="form-control" disabled>
              </div>
            </div>
            <!-- Other fields… -->
            <div class="form-group mb-3">
              <label for="addDescription">Description</label>
              <input id="addDescription" type="text" class="form-control" placeholder="Description">
            </div>
            <div class="form-group mb-3">
              <label for="addContact">Contact Number</label>
              <input id="addContact" type="text" class="form-control" placeholder="09183348123">
            </div>
            <div class="form-group mb-3">
              <label for="addBusinessHours">Business Hours</label>
              <input id="addBusinessHours" type="text" class="form-control" placeholder="9AM - 5PM">
            </div>
            <div class="form-group mb-3">
              <label for="addSelectMaterial">Types of material</label>
              <select id="addSelectMaterial" class="form-select" data-placeholder="Choose filters"
                id="small-select2-options-multiple-field" multiple>
                <?php foreach ($materials as $m): ?>
                  <option value="<?= esc($m['id']) ?>" data-icon="<?= esc($m['icon']) ?>">
                    <?= esc($m['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" onclick="addNewFacility()">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Pin‑Location Modal -->
  <div class="modal fade" id="modalMap" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pin Facility Location</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body p-0 position-relative">
          <div id="mapLocate" class="map-canvas"></div>
          <div class="position-absolute top-0 start-0 m-3" style="z-index:5; width:280px;">
            <input id="locateSearch" type="text" class="form-control" placeholder="Search address…">
            <div class="card mt-2">
              <div class="card-body">
                <span class="fw-bold">Note:</span><br>
                <small>• Search by address </small><br>
                <small>• Pin directly in map if not found </small>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" onclick="confirmPin()">Pin Location</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Globals
  let map, locateMap, centerMarker, geocoder, autocompleteClassic;

  // 1) Main map init
  function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
      center: { lat: 14.5995, lng: 120.9842 },
      zoom: 14,
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
  }

  // callback from API
  window.initAllMaps = initMap;

  // Utility: update lat/lng fields
  function updateLatLngInputs(latLng, address) {
    document.getElementById('addLat').value = latLng.lat().toFixed(6);
    document.getElementById('addLng').value = latLng.lng().toFixed(6);
    document.getElementById('addAddr').value = address;
  }

  // Open the “Create” modal
  function openCreateNew() {
    ['addName', 'addDescription', 'addContact', 'addBusinessHours'].forEach(id => {
      document.getElementById(id).value = '';
    });
    $('#addModal').modal('show').on('shown.bs.modal', () => {
      $('#addName').trigger('focus');
    });
  }

  // Open the “Pin” modal
  function selectonMap() {
    initLocateMap();
    $('#modalMap').modal('show');
  }

  // Confirm pin → just close modal
  function confirmPin() {
    if (!centerMarker) {
      Swal.fire('No location selected', 'Please search or click on the map first.', 'warning');
      return;
    } else {
      $('#locateSearch').val('');
      $('#modalMap').modal('hide');
    }

  }

  // 2) Build the locate‑map & click→reverse‑geocode
  function initLocateMap() {
    geocoder = new google.maps.Geocoder();
    locateMap = new google.maps.Map(document.getElementById('mapLocate'), {
      center: { lat: 14.5995, lng: 120.9842 },
      zoom: 14,
      disableDefaultUI: true,
      mapTypeControl: false,
      fullscreenControl: false
    });
    locateMap.getDiv().style.cursor = 'url("/public/front/assets/img/marker-icon.png") 12 40, auto';
    centerMarker = null;

    locateMap.addListener('click', e => {
      if (!centerMarker) {
        centerMarker = new google.maps.Marker({ map: locateMap });
      }
      centerMarker.setPosition(e.latLng);

      geocoder.geocode({ location: e.latLng }, (results, status) => {
        if (status === 'OK' && results[0]) {
          document.getElementById('locateSearch').value = results[0].formatted_address;
        } else {
          Swal.fire('Address not found', 'Couldn’t determine an address here.', 'error');
        }
      });
    });

    // once the map exists, wire up autocomplete
    initLocateAutocompleteClassic();
  }

  // 3) Classic Autocomplete on the BS5 <input>
  function initLocateAutocompleteClassic() {
    const input = document.getElementById('locateSearch');

    // Restrict suggestions to Philippines
    const options = {
      types: ['address'],
      // restrict to PH
      componentRestrictions: { country: 'ph' },
      // only pull the data we need
      fields: ['geometry', 'formatted_address']
    };

    autocompleteClassic = new google.maps.places.Autocomplete(input, options);

    autocompleteClassic.addListener('place_changed', () => {
      const place = autocompleteClassic.getPlace();
      if (!place.geometry) {
        Swal.fire('Address not found', 'Please select one of the dropdown suggestions.', 'error');
        return;
      }
      const loc = place.geometry.location;
      const addr = place.formatted_address;

      locateMap.panTo(loc);
      if (!centerMarker) {
        centerMarker = new google.maps.Marker({ map: locateMap, position: loc });
      } else {
        centerMarker.setPosition(loc);
      }
      updateLatLngInputs(loc, addr);
      input.value = addr;
    });
  }


  // Initialize Select2 after DOM ready
  $(function () {
    $('#addSelectMaterial').select2({
      theme: "bootstrap-5",
      width: '100%',
      placeholder: "Choose filters",
      closeOnSelect: false,
      selectionCssClass: 'select2--small',
      dropdownCssClass: 'select2--small',
      templateResult: data => {
        if (!data.id) return data.text;
        const icon = $(data.element).data('icon') || 'fa-tag';
        return $(`<span><i class="fa ${icon} me-2"></i>${data.text}</span>`);
      },
      templateSelection: data => {
        if (!data.id) return data.text;
        const icon = $(data.element).data('icon') || 'fa-tag';
        return $(`<span><i class="fa ${icon} me-1"></i>${data.text}</span>`);
      }
    });

  });
</script>
<script>
  function addNewFacility() {
    // 1) collect all your form values
    const name = $('#addName').val().trim();
    const lat = $('#addLat').val().trim();
    const lng = $('#addLng').val().trim();
    const address = $('#addAddr').val().trim();
    const description = $('#addDescription').val().trim();
    const contact = $('#addContact').val().trim();
    const businessHours = $('#addBusinessHours').val().trim();
    const materials = $('#addSelectMaterial').val() || [];

    // 2) assemble payload
    const facilityData = {
      name, lat, lng, address,
      description, contact, businessHours,
      materials
    };

    // 3) POST to controller
    axios.post('<?= base_url("facility/saveNewfacility") ?>', facilityData)
      .then(({ data }) => {
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Saved!',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            $('#addModal').modal('hide');
            $('#usersTable').DataTable().ajax.reload();
          });
        }
      })
      .catch(error => {
        // 4) Validation errors (400) with `errors` array
        if (error.response?.status === 400 && error.response.data.errors) {
          const msgs = Object.values(error.response.data.errors);
          let html = '<ul style="text-align:left;">';
          msgs.forEach(m => html += `<li>${m}</li>`);
          html += '</ul>';
          Swal.fire({
            icon: 'error',
            title: 'Validation error/s:',
            html
          });
          return;
        }

        // 5) Other errors
        const msg = error.response?.data?.message
          || error.message
          || 'Unexpected error';
        Swal.fire('Error', msg, 'error');
      });
  }
</script>