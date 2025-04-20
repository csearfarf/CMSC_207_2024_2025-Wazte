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
          <table id="facilityTable" class="table align-items-center">
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
          <div id="map" class="map-canvas position-relative" style="height:700px;max-height:800px;"></div>
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
              <button type="button" class="btn btn-secondary w-100" onclick="selectonMap('1')">
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


  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Configure Facility Details</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <!-- Facility Name -->
            <div class="form-group mb-3">
              <input id="editFacilityID" type="hidden" class="form-control">
              <label for="editName">Facility Name</label>
              <input id="editName" type="text" class="form-control" placeholder="Facility Name">
            </div>
            <hr>
            <!-- Pin button -->
            <div class="mb-3">
              <button type="button" class="btn btn-secondary w-100" onclick="selectonMap('2')">
                Pin Location
              </button>
            </div>
            <!-- Lat / Lng -->
            <div class="form-group mb-3 row">
              <div class="col-12">
                <label>Facility Address</label>
                <input id="editAddr" type="text" class="form-control" disabled>
              </div>

              <div class="col" hidden>
                <label>Lat</label>
                <input id="editLat" type="text" class="form-control" disabled>
              </div>
              <div class="col" hidden>
                <label>Lng</label>
                <input id="editLng" type="text" class="form-control" disabled>
              </div>
            </div>
            <!-- Other fields… -->
            <div class="form-group mb-3">
              <label for="editDescription">Description</label>
              <input id="editDescription" type="text" class="form-control" placeholder="Description">
            </div>
            <div class="form-group mb-3">
              <label for="editContact">Contact Number</label>
              <input id="editContact" type="text" class="form-control" placeholder="09183348123">
            </div>
            <div class="form-group mb-3">
              <label for="editBusinessHours">Business Hours</label>
              <input id="editBusinessHours" type="text" class="form-control" placeholder="9AM - 5PM">
            </div>
            <div class="form-group mb-3">
              <label for="editSelectMaterial">Types of material</label>
              <select id="editSelectMaterial" class="form-select" data-placeholder="Choose filters"
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
          <button class="btn btn-primary" onclick="saveEditFacility()">Save Changes</button>
        </div>
      </div>
    </div>
  </div>




  <!-- Pin‑Location Modal -->
  <div class="modal fade" id="selectonMap" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pin Facility Location</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
          <input type="hidden" id="selectonMapTriggered">
        </div>
        <div class="modal-body p-0 position-relative">
          <div id="mapLocate" class="map-canvas"></div>
          <div class="position-absolute top-0 start-0 m-3" style="z-index:5; width:280px;">
            <input id="locateSearch" type="text" class="form-control" placeholder="Search address…">
            <div class="card mt-2  bg-gradient-warning">
              <div class="card-body text-white">
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


    // ← add this:
    infoWindow = new google.maps.InfoWindow();
  }

  // callback from API
  window.initAllMaps = initMap;

  // Utility: update lat/lng fields
  function updateLatLngInputs(latLng, address) {
    let modalselection = $('#selectonMapTriggered').val().trim();
    if (modalselection === "1") {
      document.getElementById('addLat').value = latLng.lat().toFixed(6);
      document.getElementById('addLng').value = latLng.lng().toFixed(6);
      document.getElementById('addAddr').value = address;
    } else {

      document.getElementById('editLat').value = latLng.lat().toFixed(6);
      document.getElementById('editLng').value = latLng.lng().toFixed(6);
      document.getElementById('editAddr').value = address;
    }

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
  function selectonMap(triggered) {
    $('#selectonMapTriggered').val(triggered);
    initLocateMap();
    $('#selectonMap').modal('show');
  }

  function confirmPin() {
    if (!centerMarker) {
      Swal.fire('No location selected', 'Please search or click on the map first.', 'warning');
      return;
    }

    const modalselection = $('#selectonMapTriggered').val();
    const latLng = centerMarker.getPosition(); // Google Maps LatLng object

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: latLng }, (results, status) => {
      if (status === 'OK' && results[0]) {
        const address = results[0].formatted_address;

        // Call your utility to update lat/lng fields
        updateLatLngInputs(latLng, address, modalselection);

        // Cleanup
        $('#locateSearch').val('');
        $('#selectonMapTriggered').val('');
        $('#selectonMap').modal('hide');
      } else {
        Swal.fire('Address not found', 'Couldn’t determine an address here.', 'error');
      }
    });
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
  function initLocateAutocompleteClassic() {
    // ← grab the element, not its .value
    const inputEl = document.getElementById('locateSearch');

    // Restrict suggestions to Philippines
    const options = {
      types: ['address'],
      componentRestrictions: { country: 'ph' },
      fields: ['geometry', 'formatted_address']
    };

    // pass the element into Autocomplete, not a string
    autocompleteClassic = new google.maps.places.Autocomplete(inputEl, options);

    autocompleteClassic.addListener('place_changed', () => {
      const place = autocompleteClassic.getPlace();
      if (!place || !place.geometry || !place.formatted_address) {
        Swal.fire(
          'Address not found',
          'Please select one of the dropdown suggestions.',
          'error'
        );
        return;
      }

      const loc = place.geometry.location;
      const addr = place.formatted_address;

      locateMap.panTo(loc);
      if (!centerMarker) {
        centerMarker = new google.maps.Marker({
          map: locateMap,
          position: loc
        });
      } else {
        centerMarker.setPosition(loc);
      }

      // update your inputs (you can pass modalselection if you need it)
      updateLatLngInputs(loc, addr);
      // now actually set the input’s value
      inputEl.value = addr;
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


    $('#editSelectMaterial').select2({
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
            $('#facilityTable').DataTable().ajax.reload();
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



  function saveEditFacility() {
    // 1) collect form values
    const facilityID = $('#editFacilityID').val();
    const name = $('#editName').val().trim();
    const lat = $('#editLat').val().trim();
    const lng = $('#editLng').val().trim();
    const address = $('#editAddr').val().trim();
    const description = $('#editDescription').val().trim();
    const contact = $('#editContact').val().trim();
    const businessHours = $('#editBusinessHours').val().trim();
    const materials = $('#editSelectMaterial').val() || [];

    // 2) assemble payload
    const payload = {
      facility_ID: facilityID,
      name, lat, lng, address,
      description, contact, businessHours,
      materials
    };

    // 3) POST to your new endpoint
    axios.post('<?= base_url("facility/saveEditFacility") ?>', payload)
      .then(({ data }) => {
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Updated!',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            $('#editModal').modal('hide');
            $('#facilityTable').DataTable().ajax.reload(null, false);
          });
        }
      })
      .catch(error => {
        // validation errors
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
        // other errors
        const msg = error.response?.data?.message
          || error.message
          || 'Unexpected error';
        Swal.fire('Error', msg, 'error');
      });
  }


  function editFacility(id) {
    axios.get(`<?= base_url('facility/select') ?>/${id}`)
      .then(({ data: f }) => {
        // hidden ID
        $('#editFacilityID').val(f.facility_ID);

        // basic fields
        $('#editName').val(f.name);
        $('#editDescription').val(f.Description);
        $('#editContact').val(f.contactNo);
        $('#editBusinessHours').val(f.BusinessHours);

        // location fields
        $('#editAddr').val(f.location.address);
        $('#editLat').val(f.location.lat);
        $('#editLng').val(f.location.lng);

        // materials: extract the tags_IDs
        const selected = f.tags.map(t => t.tags_ID);
        $('#editSelectMaterial').val(selected).trigger('change');

        // finally, show the modal
        $('#editModal').modal('show');
      })
      .catch(err => {
        const msg = err.response?.data?.message || err.message || 'Failed to load facility';
        Swal.fire('Error', msg, 'error');
      });
  }

  function deleteFacility(id) {
    Swal.fire({
      title: 'Are you sure?',
      text: "This will permanently delete the facility.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete it!'
    }).then(result => {
      if (!result.isConfirmed) return;

      axios.post(`<?= base_url('facility/delete') ?>/${id}`)
        .then(({ data }) => {
          if (data.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: data.message,
              timer: 2000,
              showConfirmButton: false
            });
            // reload table and re‑plot markers
            const table = $('#facilityTable').DataTable();
            table.ajax.reload(null, false);
          } else {
            Swal.fire('Error', data.message || 'Could not delete.', 'error');
          }
        })
        .catch(err => {
          const msg = err.response?.data?.message || err.message || 'Server error';
          Swal.fire('Error', msg, 'error');
        });
    });
  }

</script>
<script>


  // after:
  let infoWindow;
  const markersMap = {};
  $(document).ready(function () {

    initMap();


    var table = $('#facilityTable').DataTable({
      dom: "<'row'<'col-12'f>>" +
        "<'row'<'col-12't>>" +
        "<'row'<'col-md-6'i><'col-md-6'p>>",
      pageLength: 6,
      ajax: {
        url: "<?= base_url('facility/list') ?>",
        dataSrc: ""
      },
      columns: [
        { data: "name", title: "Facility Name" },
        {
          data: null,
          title: "Actions",
          orderable: false,
          className: "text-right",
          render: function (_, type, row) {
            // row.facility_ID is your ID
            return `
              <div class="dropdown">
                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown"
        onclick="zoomToFacility(${row.facility_ID})">
                  <i class="fas fa-ellipsis-v"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                  <a class="dropdown-item" href="#" onclick="editFacility(${row.facility_ID})">
                    Edit
                  </a>
                  <a class="dropdown-item" href="#" onclick="deleteFacility(${row.facility_ID})">
                    Delete
                  </a>
                </div>
              </div>
            `;
          }
        }
      ],
      language: {
        paginate: {
          previous: "<i class='fas fa-angle-left'></i>",
          next: "<i class='fas fa-angle-right'></i>"
        }
      },
      //  rowCallback runs for every row on each draw
      rowCallback: function (row, data) {
        // remove any old handler, then attach fresh
        $(row).off('click').on('click', function () {
          const marker = markersMap[data.facility_ID];
          if (!marker) return;
          map.panTo(marker.getPosition());
          map.setZoom(16);
          google.maps.event.trigger(marker, 'click');

        });
      }
    });

    // listen on the API instance, namespaced with .dt
    table.on('xhr.dt', function (e, settings, json, xhr) {
      plotFacilityMarkers(json);
    });


  });

  function zoomToFacility(id) {
    const marker = markersMap[id];
    if (!marker) return;
    map.panTo(marker.getPosition());
    map.setZoom(16);
    google.maps.event.trigger(marker, 'click');
  }


  function plotFacilityMarkers(facilities) {
    // 1) Clear out old markers
    for (const id in markersMap) {
      markersMap[id].setMap(null);
      delete markersMap[id];
    }

    // 2) Add one marker per facility
    facilities.forEach(f => {
      const pos = {
        lat: parseFloat(f.location.lat),
        lng: parseFloat(f.location.lng)
      };

      const m = new google.maps.Marker({
        position: pos,
        map: map
      });

      // store in lookup by facility_ID
      markersMap[f.facility_ID] = m;

      m.addListener('click', () => {
        // build one badge per tag
        const badges = f.tags.map(t => `
        <span class="badge bg-primary me-1 text-white">${t.Material || t.name}</span>
      `).join('');

        const content = `
        <div style="min-width:200px">
          <h3>${f.name}</h3>
          <p class="m-0">${f.Description}</p>
          <p class="m-0"><strong>Facility Hours:</strong> ${f.BusinessHours}</p>
          <p>${badges}</p>
        </div>
      `;
        infoWindow.setContent(content);
        infoWindow.open(map, m);
      });

    });
  }


</script>