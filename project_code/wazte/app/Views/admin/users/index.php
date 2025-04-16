<!-- Page content -->
    <div class="container-fluid mt--6">
      <div class="row">
        
        <div class="col-xl-12">
          <div class="card ">
            <div class="d-flex flex-row-reverse">
               <div class="p-3">
                        <!-- Button trigger modal -->
                  <button onclick="openCreateNew()" type="button" class="btn btn-primary">
                    Add Record
                  </button>
                </div>
            </div>
            <div class="card-body table-responsive">
              <div >
                <table id="usersTable" class="table align-items-center  ">
                  <thead class="thead-light">
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Profile Image</th>
                      <th class="text-right">Option</th>
                    </tr>
                  </thead>
                  <tbody  class="list">
                    <!-- Data will be loaded here dynamically -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>


       <!-- Edit Modal -->
       <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Configure User Details</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            <form>
              <div class="form-group">
                <input type="hidden" id="editUserID">
                <label for="editName">Name</label>
                <input type="text" class="form-control" id="editName" placeholder="John Cruz">
              </div>
              <div class="form-group">
                <label for="editEmail">Email editress</label>
                <input type="email" class="form-control" id="editEmail" placeholder="test@gmail.com">
              </div>
              <div class="form-group">
                <label for="editImageLink">Image Link</label>
                <input type="text" class="form-control" id="editImageLink" placeholder="https://image.com/test.png">
              </div>
              <div class="form-group">
                <label for="editRole">Role select</label>
                <select class="form-control" id="editRole">
                  <option value="1">1 - Admin</option>
                  <option value="2">2 - Facilitator</option>
                  <option value="3">3 - User</option>
                  <option value="4">4 - Let User decide</option>
                </select>
              </div>
              
            </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="updateUser()">Save changes</button>
            </div>
          </div>
        </div>
      </div>

      
    

      <!-- Add Modal -->
      <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addModalLabel">Create New User</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="addName">Name</label>
                <input type="text" class="form-control" id="addName" placeholder="John Cruz">
              </div>
              <div class="form-group">
                <label for="addEmail">Email address</label>
                <input type="email" class="form-control" id="addEmail" placeholder="test@gmail.com">
              </div>
              <div class="form-group">
                <label for="addImageLink">Image Link</label>
                <input type="text" class="form-control" id="addImageLink" placeholder="https://image.com/test.png">
              </div>
              <div class="form-group">
                <label for="addRole">Role select</label>
                <select class="form-control" id="addRole">
                  <option value="1">1 - Admin</option>
                  <option value="2">2 - Facilitator</option>
                  <option value="3">3 - User</option>
                  <option value="4">4 - Let User decide</option>
                </select>
              </div>
              
            </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="saveNewUser()">Save changes</button>
            </div>
          </div>
        </div>
      </div>






      <script>       

      

      $(document).ready(function(){
        var table = $('#usersTable').DataTable({
          // Layout: table on top and info + pagination on the bottom.
          dom: "<'row'<'col-12'f>>" +
              "<'row'<'col-12't>>" + 
              "<'row'<'col-md-6'i><'col-md-6'p>>",
          ajax: {
            url: "<?= base_url('admin/users/getUsers') ?>",
            dataSrc: ""  // Tell DataTables that the response is a plain array.
          },
          columns: [
            { data: "Name" },
            { data: "Email" },
            { data: "rolename" },
            { 
              data: "profile_img",
              render: function(data, type, row, meta) {
                return '<img src="' + data + '" alt="Profile Image" style="height: 40px;" referrerpolicy="no-referrer">';
              }
            },
            { 
              data: "actions",
              className: "text-right"
            }
          ],
          language: {
            paginate: {
              previous: "<i class='fas fa-angle-left'></i>",
              next: "<i class='fas fa-angle-right'></i>"
            }
          }
        });
      });


      function editUser(userId) {

        // Fetch user details using Axios.
        axios.get(`<?= base_url("admin/users/view/") ?>${userId}`)
          .then(function (response) {
            // Check if the response indicates success.
            if (response.data.status === 'success') {
              var user = response.data.data; // Assuming user data is returned in "data"
              // Populate modal form fields with user details.
              $('#editName').val(user.Name);
              $('#editEmail').val(user.Email);
              $('#editImageLink').val(user.profile_img);
              // For role, set by value. Adjust according to your data structure (roleID or rolename).
              $('#editRole').val(user.roleID);
              $('#editUserID').val(user.user_ID);
              
              // Show the modal.
              $('#editModal').modal('show');
            } else {
              console.error("Error fetching user details:", response.data.message);
            }
          })
          .catch(function (error) {
            console.error("Axios error:", error);
          });

        // Set focus on #editName when the modal is fully shown.
        $('#editModal').on('shown.bs.modal', function () {
          $('#editName').trigger('focus');
        });
      }



        function openCreateNew() {
            // Clear input values (use .val('') if these are input fields)
            $('#addName').val('');
            $('#addEmail').val('');
            $('#addImageLink').val('');
            
            // Show the modal.
            $('#addModal').modal('show');
            
            // When the modal is fully shown, set focus on #addName.
            $('#addModal').on('shown.bs.modal', function () {
                $('#addName').trigger('focus');
            });
        }


        function saveNewUser() {
          // Retrieve form values from your modal inputs.
          let name    = $('#addName').val().trim();
          let email   = $('#addEmail').val().trim();
          let picture = $('#addImageLink').val().trim();
          let role    = $('#addRole').val().trim(); // Include if your controller accepts role input.

          // Build the data object. Adjust the keys as needed based on what your controller expects.
          let userData = {
              name: name,
              email: email,
              picture: picture,
              role: role
          };

          // Send the data to the controller using Axios POST.
          axios.post('<?= base_url("admin/users/createNewuser") ?>', userData)
              .then(function(response) {
                  if (response.data.status === 'success') {
                      // On success, show a success prompt via SweetAlert2 for 2.5 seconds.
                      Swal.fire({
                          icon: 'success',
                          title: 'Success',
                          text: response.data.message,
                          timer: 2500,
                          timerProgressBar: true,
                          showConfirmButton: false
                      }).then(function() {
                          // Hide the modal.
                          $('#addModal').modal('hide');
                          // Reload the DataTable using the Ajax reload function.
                          $('#usersTable').DataTable().ajax.reload();
                      });
                  } else {
                      // If response structure is unexpected, show an error prompt.
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: response.data.message || "Unexpected response from server."
                      });
                  }
              })
              .catch(function(error) {
                  let message = "";
                  if (error.response) {
                      message = error.response.data.message || "Server error occurred.";
                  } else if (error.request) {
                      message = "No response received from server.";
                  } else {
                      message = "Error: " + error.message;
                  }
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: message
                  });
              });
      }

      function deleteUser(userId) {
          // Show confirmation prompt before deleting the user.
          Swal.fire({
              title: 'Are you sure?',
              text: "Do you really want to delete this user?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Yes, delete it!',
              cancelButtonText: 'No, cancel'
          }).then((result) => {
              if (result.isConfirmed) {
                  // If confirmed, call the delete endpoint using Axios.
                  axios.delete(`<?= base_url("admin/users/remove") ?>/${userId}`)
                      .then(function(response) {
                          if (response.data.status === 'success') {
                              // On success, show a success prompt via SweetAlert2 for 2.5 seconds.
                              Swal.fire({
                                  icon: 'success',
                                  title: 'Deleted',
                                  text: response.data.message,
                                  timer: 2500,
                                  timerProgressBar: true,
                                  showConfirmButton: false
                              }).then(function() {
                                  // Reload the DataTable to update the user list.
                                  $('#usersTable').DataTable().ajax.reload();
                              });
                          } else {
                              // If response structure is unexpected, show an error prompt.
                              Swal.fire({
                                  icon: 'error',
                                  title: 'Error',
                                  text: response.data.message || "Unexpected response from server."
                              });
                          }
                      })
                      .catch(function(error) {
                          let message = "";
                          if (error.response) {
                              message = error.response.data.message || "Server error occurred.";
                          } else if (error.request) {
                              message = "No response received from server.";
                          } else {
                              message = "Error: " + error.message;
                          }
                          Swal.fire({
                              icon: 'error',
                              title: 'Error',
                              text: message
                          });
                      });
              }
          });
      }

      function updateUser() {
        // Gather values from the modal inputs.
        const userId    = $('#editUserID').val();
        const name      = $('#editName').val();
        const email     = $('#editEmail').val();
        const imageLink = $('#editImageLink').val();
        const role      = $('#editRole').val();

        // Basic client-side validation.
        if (!userId || !name || !email || !role) {
          Swal.fire({
                              icon: 'error',
                              title: 'Error',
                              text: "Please fill in all required fields."
                          });
          return;
        }

        // Prepare the data payload.
        const postData = {
          user_ID: userId,
          Name: name,
          Email: email,
          profile_img: imageLink,
          roleID: role
        };

        // Use Axios to make a POST request to update the user.
        axios.post('<?= base_url("admin/users/update") ?>', postData)
          .then(function (response) {
            if (response.data.status === 'success') {
              // On success, show a success prompt via SweetAlert2 for 2.5 seconds.
              Swal.fire({
                                  icon: 'success',
                                  title: 'Success !',
                                  text: response.data.message,
                                  timer: 2500,
                                  timerProgressBar: true,
                                  showConfirmButton: false
                              }).then(function() {
                                  // Reload the DataTable to update the user list.
                                  $('#usersTable').DataTable().ajax.reload();
                                   // Hide the modal after success.
                                  $('#editModal').modal('hide');
                              });
             
              // Optionally, refresh your DataTable or page.
            } else {
              Swal.fire({
                              icon: 'error',
                              title: 'Error',
                              text: response.data.message
                          });
            }
          })
          .catch(function (error) {
            console.error("Axios error:", error);
            Swal.fire({
                              icon: 'error',
                              title: 'Error',
                              text: error
                          });
          });
      }

  







      </script>
      
