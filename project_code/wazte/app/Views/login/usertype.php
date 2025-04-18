

<header class="header-global">
        <nav id="navbar-main"
            class="navbar navbar-main navbar-expand-lg headroom py-lg-3 px-lg-6 navbar-dark navbar-theme-primary">
            <div class="container d-flex align-items-center">
                <div class="p-2 me-auto">
                    <img src=" <?= base_url('public/front/assets/img/front_logo.png') ?>" height="60" class="mr-3" alt="Footer logo">
                </div>
                <div class="p-2">
                    <a  href="<?= base_url('navigate') ?>"
                        class="btn btn-md btn-secondary animate-up-2 text-white"><i class="fas fa-paper-plane mr-2"></i>
                        Locate
                        Facilities</a>
                </div>



            </div>
        </nav>
    </header>
    <main>

        <div class="preloader bg-soft flex-column justify-content-center align-items-center">
            <div class="loader-element">
                <img src="<?= base_url('public/front/assets/img/wazte_logo.png') ?>" height="120">
            </div>
        </div>

        <!-- Hero -->
        <section class="section-header bg-primary text-white ">
            <div class="container">

                <div class="row text-gray">
                    <div class="col-12 col-md-12 text-white text-center mb-4">
                        <h1 class="display-2 mb- text-white">Choose Your Role to Continue</h1>
                        <p class="lead px-5">
                            Select how you'd like to use the platform—whether you're exploring recycling options or
                            managing a facility. This helps us tailor your experience.

                        </p>
                    </div>

                    <div class="col-12 col-lg-6 mt-2">
                        <!-- Card -->
                        <div class="card shadow-soft border-light ">
                            <div class="card-header border-light py-5 px-4">
                                <!-- Price -->

                                <h4 class="mb-3 text-black">Recycling Explorer</h4>
                                <p class="font-weight-normal mb-0">Perfect for individuals who want to find the
                                    right
                                    place to drop off recyclables
                                    and ask questions directly to the facilities.</p>
                            </div>
                            <div class="card-body pt-5">
                                <ul class="list-group simple-list">
                                    <li class="list-group-item font-weight-normal"><span class="icon-primary"><i
                                                class="fas fa-check"></i></span>Navigate & explore recycling centers<br>
                                        <small>Use our interactive map to find nearby locations easily.</small>
                                    <li class="list-group-item font-weight-normal"><span class="icon-primary"><i
                                                class="fas fa-check"></i></span>Contact facilitators for inquiries<br>
                                        <small>Send direct messages for more information or assistance.</small>
                                    <li class="list-group-item font-weight-normal"><span class="icon-primary"><i
                                                class="fas fa-check"></i></span>Filter by material type<br>
                                        <small>Quickly find facilities that accept plastic, e-waste, and more.</small>


                                </ul>
                            </div>
                            <div class="card-footer px-4 pb-4">
                                <!-- Button -->
                                <a onclick="chooseTypeRedirect('0')" class="btn btn-block btn-outline-secondary">
                                    Be a Recycling Explorer<span class="icon icon-xs ml-3"><i
                                            class="fas fa-arrow-right"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mt-2">
                        <!-- Card -->
                        <div class="card shadow-soft border-light">
                            <div class="card-header border-light py-5 px-4">

                                <h4 class="mb-3 text-black">Eco Facilitators</h4>
                                <p class="font-weight-normal mb-0">Ideal for facility operators or admins who want to
                                    manage their recycling site and engage with users efficiently.</p>
                            </div>
                            <div class="card-body pt-5">
                                <ul class="list-group simple-list">
                                    <li class="list-group-item font-weight-normal"><span class="icon-primary"><i
                                                class="fas fa-check"></i></span>Navigate & explore other facility
                                        centers <br>
                                        <small>Access full map view for reference or collaboration.</small>
                                    <li class="list-group-item font-weight-normal"><span class="icon-primary"><i
                                                class="fas fa-check"></i></span>Connect with users for their
                                        inquiries<br>
                                        <small>Receive and respond to questions from registered users.</small>
                                    <li class="list-group-item font-weight-normal"><span class="icon-primary"><i
                                                class="fas fa-check"></i></span>Manage/Registered Facilities in
                                        Map<br><small>Edit your center’s info, add accepted materials, and maintain your
                                            listing.</small>
                                </ul>
                            </div>
                            <div class="card-footer px-4 pb-4">
                                <!-- Button -->
                                <a onclick="chooseTypeRedirect('1')" class="btn btn-block btn-outline-secondary">
                                    Be a Eco Facilitator<span class="icon icon-xs ml-3"><i
                                            class="fas fa-arrow-right"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>


    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function chooseTypeRedirect(choice) {
        axios.get('<?= base_url("login/chooseType/") ?>' + choice)
            .then(function(response) {
                if (response.data.status === 'success' && response.data.redirectUrl) {
                    // Show a success prompt via Swal2. Wait 2.5 seconds, then redirect.
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Redirecting  to your dedicated page !',
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.data.redirectUrl;
                    });
                } else {
                    // Unexpected response structure.
                    console.error('Unexpected response:', response.data);
                    if (typeof errorCallback === 'function') {
                        errorCallback(response.data.message || "Unexpected response from server.");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message || "Unexpected response from server."
                        });
                    }
                }
            })
            .catch(function(error) {
                let message = "";
                if (error.response) {
                    // The server responded with a status code outside the range 2xx.
                    console.error('Server error:', error.response.data);
                    message = error.response.data.message || "Server error occurred.";
                } else if (error.request) {
                    // The request was made but no response received.
                    console.error('No response received:', error.request);
                    message = "No response received from server.";
                } else {
                    // Other errors in setting up the request.
                    console.error('Error:', error.message);
                    message = "Error: " + error.message;
                }
                if (typeof errorCallback === 'function') {
                    errorCallback(message);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                }
            });
    }
    </script>
