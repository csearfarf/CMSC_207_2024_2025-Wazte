$(document).ready(function () {

    // Facility search filtering
    $('#facilitySearchInput').on('input', function () {
      const term = $(this).val().trim();
      filterFacilities(term || null);
    });

    function filterFacilities(term) {
      let filtered = latestResults;
      if (term) {
        const regex = new RegExp(term, 'i');
        filtered = latestResults.filter(p => regex.test(p.name));
      }
      clearMarkers(); renderMarkers(filtered); renderPlacesList(filtered);
    }





    $('#inquiryForm').on('submit', function (e) {
      e.preventDefault();

      // Basic validation
      const facilitatorEmail = $('#facilitator_email').val().trim();
      const subject = $('#subject').val().trim();
      const message = $('#message').val().trim();

      let hasError = false;

      if (!facilitatorEmail || !/^\S+@\S+\.\S+$/.test(facilitatorEmail)) {
        $('#facilitator_email_input').addClass('is-invalid');
        hasError = true;
      } else {
        $('#facilitator_email_input').removeClass('is-invalid');
      }

      if (!subject) {
        $('#subject').addClass('is-invalid');
        hasError = true;
      } else {
        $('#subject').removeClass('is-invalid');
      }

      if (!message || message.length < 5) {
        $('#message').addClass('is-invalid');
        hasError = true;
      } else {
        $('#message').removeClass('is-invalid');
      }

      if (hasError) {
        Swal.fire({
          icon: 'error',
          title: 'Validation Error',
          text: 'Please correct the fields highlighted in red.',
        });
        return;
      }

      const formData = {
        facilitator_email: facilitatorEmail,
        subject: subject,
        message: message
      };

          // Show loading alert
          Swal.fire({
            title: 'One sec…',
            html: 'We’re sending your inquiry now!',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
      axios.post( baseurl+'inquiry/send', formData)
        .then(function (response) {
          if (response.data.success) {
            Swal.close();
            Swal.fire({
              icon: 'success',
              title: 'Sent!',
              text: 'Inquiry sent successfully.',
            }).then(() => {
              $('#sendInquiry').modal('hide');
              $('#inquiryForm')[0].reset();
              $('.form-control').removeClass('is-invalid');
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Failed to send',
              text: response.data.message || 'An error occurred sending your inquiry.',
            });
          }
        })
        .catch(function (error) {
          console.error(error);
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'An unexpected error occurred. Please try again later.',
          });
        });
    });
  });


//   SEND INQUIRY MODAL

function sendInquiry(email) {
    $('#facilitator_email').val(email);
    $('#sendInquiry').modal('show')
  }


//   MAP FUNCTIONS 
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
        loadGooglePlaces(currentUserLocation, function (results) {
          allResults = allResults.concat(results);
          pending--;

          console.log(allResults)
          if (pending === 0) processResults(allResults);
        });
      }
      if (selectedFilters.includes("Wazte Places")) {
        pending++;
        loadWaztePlaces(currentUserLocation, function (results) {
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

    function loadWaztePlaces(userLocation, callback) {
      axios.get(baseurl+'navigate/wazteList')
        .then(response => {
          const facilities = response.data;
          const mapped = facilities.map(facility => {
            return {
              place_id: `wazte_${facility.facility_ID}`,
              name: facility.name,
              geometry: {
                location: new google.maps.LatLng(
                  parseFloat(facility.location.lat),
                  parseFloat(facility.location.lng)
                )
              },
              vicinity: facility.location.address,
              facilitator_email: facility.facilitator_email || 'info@default.com', // fallback or use actual field if available
              contact: facility.contactNo,
              businessHours: facility.BusinessHours,
              description: facility.Description,
              types: facility.tags.map(tag => tag.Material.toLowerCase()),
              materialIcons: facility.tags.map(tag => ({
                name: tag.Material,
                icon: tag.icon
              }))
            };
          });
          callback(mapped);
        })
        .catch(err => {
          console.error("Failed to load Wazte facilities:", err);
          callback([]);
        });
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

        const infoWindow = new google.maps.InfoWindow();

        marker.addListener("click", () => {
          if (activeInfoWindow) {
            activeInfoWindow.close();
            if (directionsRenderer) directionsRenderer.set('directions', null);
          }

          let extraDetails = '';
          const details = placeDetailsMap[place.place_id];

          if (details) {
            console.table(details)
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

          if ((isWazte)) {
            extraDetails += `<br>Contact: ${place.contact}<br>`;

          }
          const inquiryBtn = (isWazte && userRole === '3')
            ? `<button 
       class="btn btn-sm btn-secondary " 
       onclick="sendInquiry('${place.facilitator_email || ''}')">
       Send Inquiry
     </button>`
            : '';

          const updatedContent = `
        <strong class="fw-bold">${place.name}</strong><br>
        ${place.vicinity || place.formatted_address || 'No address available'}<br>
        ${place.rating ? '⭐ ' + place.rating : ''}${extraDetails}<br>
        ${sourceBadge} ${materialBadges} ${googleBadges} ${googleTypesText}<br><hr>
        <div class="d-flex gap-2 flex-row-reverse">
       
        <button class="btn btn-sm btn-secondary" onclick="navigateToPlace('${place.place_id}')">Navigate</button>
        ${inquiryBtn}
        </div>
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
              materialBadges += `<span class="badge bg-primary me-1 mb-1">${cat.name}</span>`;
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
        <span class="mb-1 fw-bold">${place.name}</span><br>
        <small>${place.vicinity || place.formatted_address || ''}</small><br>
        ${sourceBadge} ${materialBadges} ${googleBadges} ${googleTypesText}
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
        getPlaceDetails(place.place_id, function (details) {
          placeDetailsMap[place.place_id] = details;
        });
      });
    }

    function getPlaceDetails(placeId, callback) {
      const service = new google.maps.places.PlacesService(map);
      service.getDetails({
        placeId: placeId,
        fields: ['name', 'rating', 'formatted_address', 'vicinity', 'opening_hours', 'formatted_phone_number', 'website', 'types', 'category']
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
      axios.get( baseurl+'navigate/materialTypes')
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

    $(document).ready(function () {
      $('#small-select2-options-multiple-field').select2({
        theme: "bootstrap-5",
        width: '100%',
        placeholder: "Choose filters",
        closeOnSelect: false,
        selectionCssClass: 'select2--small',
        dropdownCssClass: 'select2--small'
      });
      $('#small-select2-options-multiple-field').val(["Google Places", "Wazte Places"]).trigger('change');
      $('#small-select2-options-multiple-field').on('change', function () {
        reloadPlaces();
      });
    });



