@extends('user.layouts.user')
  
@section('content')

<div class="container-fluid py-4">
    <div class="row">
      

      <div class="col-md-5 mt-4">
        <div class="card">
          <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Pending Transaction</h6>
          </div>
          <div class="card-body pt-4 p-3">
            <ul class="list-group">

              @if(session()->has('deletesuccess'))
              <section class="px-4">
                  <div class="row">
                      <div class="alert alert-success text-light" id="successMessage">{{ session()->get('deletesuccess') }}</div>
                  </div>
              </section>
              @endif

              @foreach ($trans as $tran)
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                  <div class="d-flex flex-column">
                    <p class="text-xs"><b>Transaction: </b>#{{$tran->tranid}}</p>
                    <span class="text-xs"><b>Amount: </b>{{$tran->amount}} <br> <b>Fine: </b>{{$tran->due}}</span>
                    <span class="text-xs">Date: <span class="text-dark ms-sm-2 font-weight-bold">{{$tran->date}}</span></span>
                    <span class="text-xs">Last Digit: <span class="text-dark ms-sm-2 font-weight-bold">{{$tran->last_digit}}</span></span>
                    <span class="text-xs">Note: <span class="text-dark font-weight-bold ms-sm-2">{{$tran->note}}</span></span>

                    <!-- Image preview link -->
                    <a href="javascript:void(0);" 
                      class="openImageModal" 
                      data-img="{{ asset($tran->document) }}">
                      <img src="{{ asset($tran->document) }}" 
                          alt="Document" 
                          style="width: 300px; height: auto; cursor:pointer;">
                    </a>
                  </div>
                  <div class="ms-auto text-end">
                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{route('user.installmentDelete', $tran->id)}}">
                      <i class="far fa-trash-alt me-2"></i>Delete
                    </a>
                    <a class="btn btn-link text-dark px-3 mb-0 editButton"
                      href="javascript:void(0);"
                      data-id="{{ $tran->id }}"
                      data-tranid="{{ $tran->tranid }}"
                      data-amount="{{ $tran->amount }}"
                      data-date="{{ $tran->date }}"
                      data-last_digit="{{ $tran->last_digit }}"
                      data-note="{{ $tran->note }}"
                      data-document="{{ asset($tran->document) }}">
                      <i class="fas fa-pencil-alt text-dark me-2"></i>Edit
                    </a>

                  </div>
                </li>
              @endforeach
              



            </ul>
          </div>
        </div>
      </div>


      <!-- Modal for showing image -->
      <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-body text-center">
              <img id="modalImage" src="" alt="Document" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal for Editing Transaction -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Transaction</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">

            <div id="editSuccessMessage"></div>


            <form id="editForm" enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="edit_id" name="id">

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label>Date</label>
                  <input type="date" class="form-control" id="edit_date" name="date" required>
                </div>

                <div class="col-md-6 mb-3">
                  <label>Last Digit</label>
                  <input type="text" class="form-control" id="edit_last_digit" name="last_digit">
                </div>

                <div class="col-md-12 mb-3">
                  <label>Amount + Fine</label>
                  <input type="number" class="form-control" id="edit_amount" name="amount" required>
                </div>

                <div class="col-md-12 mb-3">
                  <label>Note</label>
                  <input type="text" class="form-control" id="edit_note" name="note">
                </div>

                <div class="col-md-12 mb-3">
                  <label>Existing Document</label><br>
                  <img id="edit_old_image" src="" alt="Old Document" class="img-fluid rounded" style="max-width:200px">
                </div>

                <div class="col-md-12 mb-3">
                  <label>Change Document (<span style="color:red">optional</span>)</label>
                  <input type="file" class="form-control" id="edit_document" name="document">
                </div>
              </div>

              <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>




      
      <div class="col-md-6 mt-4">
        <div class="card h-100 mb-4">
          <div class="card-header pb-0 px-3">
            <div class="row">
              <div class="col-md-6">
                <h6 class="mb-0">Add Money</h6>
              </div>
              <div class="col-md-6 d-flex justify-content-end align-items-center">
                
              </div>
            </div>
          </div>
          <div class="card-body pt-4 p-3">
            @if(session()->has('success'))
              <section class="px-4">
                  <div class="row">
                      <div class="alert alert-success text-light" id="successMessage">{{ session()->get('success') }}</div>
                  </div>
              </section>
              @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class=" text-light">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
    
    
            <form  method="POST" action="{{ route('user.installmentStore') }}" id="postForm" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                  <div class="col-md-6">
                    <label>Date</label>
                    <div class="input-group mb-4">
                      <input id="date" type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ date('Y-m-d') }}" required autocomplete="date">
                        @error('date')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label>Last Digit</label>
                    <div class="input-group mb-4">
                      <input id="last_digit" type="text" class="form-control @error('last_digit') is-invalid @enderror" name="last_digit" value="" >
                        @error('last_digit')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>

                  <div class="col-md-12">
                    <label>Document (<span style="color: red">maximum: 1mb</span>)</label>
                    <div class="input-group mb-4">
                      <input id="document" type="file" class="form-control" name="document">
                    </div>
                  </div>

                </div>
                <div class="mb-4">
                  <label>Amount + Fine</label>
                  <div class="input-group">
                    <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" required >
                  </div>
                    @error('amount')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 d-none">
                  <label>Due Amount</label>
                  <div class="input-group">
                    <input id="due" type="number" class="form-control @error('due') is-invalid @enderror" name="due" >
                  </div>
                    @error('due')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <label>Note</label>
                    <div class="input-group mb-4">
                      <input id="note" type="text" class="form-control @error('note') is-invalid @enderror" name="note" >
                        @error('note')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>
                  
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <button type="submit" class="btn bg-gradient-dark w-100 btn-submit">Submit</button>
                  </div>
                </div>
                
            </form>
          </div>
        </div>
      </div>


    </div>
    <footer class="footer pt-3  ">
      <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
          <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="copyright text-center text-sm text-muted text-lg-start">
              © <script>
                document.write(new Date().getFullYear())
              </script>,
              made with <i class="fa fa-heart"></i> by
              <a href="#" class="font-weight-bold" target="_blank">Shakil</a>
              for a better web.
            </div>
          </div>
          
        </div>
      </div>
    </footer>
  </div>


@endsection
@section('script')
<script>
  $(function() {
      $( "form" ).submit(function() {
          
        $(".btn-submit").prepend('<i class="fa fa-spinner fa-spin"></i>');
        $(".btn-submit").attr("disabled", 'disabled');

      });
  });
  </script>


<script>
  document.addEventListener("DOMContentLoaded", function() {
    const imageLinks = document.querySelectorAll(".openImageModal");
    const modalImage = document.getElementById("modalImage");
    const modal = new bootstrap.Modal(document.getElementById("imageModal"));

    imageLinks.forEach(link => {
      link.addEventListener("click", function() {
        const imgSrc = this.getAttribute("data-img");
        modalImage.src = imgSrc;
        modal.show();
      });
    });
  });
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
  const editButtons = document.querySelectorAll(".editButton");
  const editModal = new bootstrap.Modal(document.getElementById("editModal"));

  editButtons.forEach(btn => {
    btn.addEventListener("click", function() {
      // Fill modal fields with current data
      document.getElementById("edit_id").value = this.dataset.id;
      document.getElementById("edit_date").value = this.dataset.date;
      document.getElementById("edit_last_digit").value = this.dataset.last_digit;
      document.getElementById("edit_amount").value = this.dataset.amount;
      document.getElementById("edit_note").value = this.dataset.note;
      document.getElementById("edit_old_image").src = this.dataset.document;

      // Show modal
      editModal.show();
    });
  });
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
  
  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('user.installmentUpdate') }}", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    })
    .then(async res => {
      const text = await res.text();
      console.log("Raw response:", text);

      try {
        const data = JSON.parse(text);

        if (data.success) {
          // ✅ Show success message
          const msgBox = document.getElementById('editSuccessMessage');
          msgBox.innerHTML = `
            <section class="px-4">
              <div class="row">
                <div class="alert alert-success text-light" id="successMessage">
                  ${data.message ?? 'Transaction updated successfully!'}
                </div>
              </div>
            </section>
          `;

          // Close modal after 1.5s (optional)
          setTimeout(() => {
            const modalEl = document.getElementById('editModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
          }, 1500);

          location.reload();
          
        } else {
          alert(data.message ?? "Update failed.");
        }

      } catch (err) {
        console.error("Invalid JSON:", err);
      }
    })
    .catch(err => console.error("Fetch error:", err));
  });

});
</script>


@endsection