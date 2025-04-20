<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="row">
    <!-- Sidebar (conversation list) -->
    <div class="col-xl-3">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Conversations</h5>
        </div>
        <div class="card-body" id="sidebarConvoList" style="max-height: 500px; overflow-y: auto;">
          <!-- Conversations will be injected here -->
        </div>
      </div>
    </div>

    <!-- Chat area -->
    <div class="col-xl-9">
      <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Messages</h5>
        </div>
        <div class="card-body" id="chatBox" style="height: 400px; overflow-y: auto; background-color: #f9f9f9;">
          <!-- Chat messages go here -->
        </div>
        <div class="card-footer">
          <form id="chatForm" class="d-flex align-items-center">
            <input type="hidden" id="inquiry_ID">
            <input type="text" id="msgInput" class="form-control me-2" placeholder="Type a message...">
            <button type="submit" class="btn btn-primary">Send</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  let selectedInquiryID = null;
  const currentUserID = <?= session('LoggedUserData')['user_ID'] ?>;

  // Load sidebar convo list
  function loadConversations() {
    axios.get('<?= base_url('inquiry/conversations') ?>')
      .then(({ data }) => {
        $('#sidebarConvoList').empty();
        $.each(data, function (_, convo) {
          $('#sidebarConvoList').append(`
          <div class="convo-item p-2 border-bottom cursor-pointer" data-id="${convo.inquiry_ID}" style="cursor:pointer;">
            <strong>${convo.name}</strong><br><small>${convo.facility}</small>
          </div>
        `);
        });
      });
  }

  // Load messages for selected conversation
  $(document).on('click', '.convo-item', function () {
    selectedInquiryID = $(this).data('id');
    $('#inquiry_ID').val(selectedInquiryID);

    axios.get(`<?= base_url('inquiry/messages') ?>/${selectedInquiryID}`)
      .then(({ data }) => {
        $('#chatBox').html('');
        $.each(data, function (_, msg) {
          const alignment = msg.sender_ID == currentUserID ? 'right' : 'left';
          $('#chatBox').append(`
          <div class="message-row ${alignment}">
            <div class="message-bubble">
              ${msg.message}
            </div>
          </div>
        `);
        });

        // Scroll to bottom
        const chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
      });
  });

  // Send message
  $('#chatForm').on('submit', function (e) {
    e.preventDefault();
    const inquiryID = $('#inquiry_ID').val();
    const message = $('#msgInput').val().trim();

    if (!message) return;

    axios.post('<?= base_url('inquiry/send') ?>', {
      inquiry_ID: inquiryID,
      message: message
    }).then(() => {
      $('#msgInput').val('');
      $('.convo-item[data-id="' + inquiryID + '"]').click(); // refresh
    });
  });

  // Init
  $(function () {
    loadConversations();
  });
</script>