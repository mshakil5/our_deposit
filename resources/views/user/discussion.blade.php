@extends('user.layouts.user')
  
@section('content')

                    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
<div class="container-fluid py-4">
    <div class="row">
      

      <div class="col-md-5 mt-4">
        <div class="card">
          <div class="card-header pb-0 px-3">
            <h6 class="mb-0">All Discussion</h6>
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


              {{-- Discussions Accordion --}}
              <div class="accordion" id="discussionAccordion">
                @foreach ($discussions as $row) {{-- rename your collection to $discussions to avoid $data as $data --}}
                  @php
                    $headingId = 'heading-'.$row->id;
                    $collapseId = 'collapse-'.$row->id;
                  @endphp

                  <div class="accordion-item mb-2 border-0 shadow-sm rounded-3">
  <h2 class="accordion-header" id="{{ $headingId }}">
    <div class="d-flex align-items-center justify-content-between w-100">
      {{-- Accordion toggle only for title --}}
      <button class="accordion-button collapsed py-3 flex-grow-1 text-start"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#{{ $collapseId }}"
              aria-expanded="false"
              aria-controls="{{ $collapseId }}">
        <div class="d-flex flex-wrap gap-3">
          <span class="text-xs">Date:
            <span class="text-dark ms-sm-2 fw-bold">{{ $row->date }}</span>
          </span>
          <span class="text-xs"><b>Summery:</b> {{ $row->summery }}</span>
          <span class="text-xs">Person with me:
            <span class="text-dark ms-sm-2 fw-bold">{{ $row->member }}</span>
          </span>
        </div>
      </button>

      {{-- Actions outside the accordion button --}}
      <div class="text-end pe-3">
        <a class="btn btn-link text-dark px-2 mb-0"
           href="{{ route('user.discussionEdit', $row->id) }}">
          <i class="fas fa-pencil-alt text-dark me-1"></i>Edit
        </a>
        <a class="btn btn-link text-danger text-gradient px-2 mb-0"
           href="{{ route('user.discussionDelete', $row->id) }}"
           onclick="return confirm('Are you sure you want to delete this discussion?');">
          <i class="far fa-trash-alt me-1"></i>Delete
        </a>
      </div>
    </div>
  </h2>

  <div id="{{ $collapseId }}" class="accordion-collapse collapse"
       aria-labelledby="{{ $headingId }}"
       data-bs-parent="#discussionAccordion">
    <div class="accordion-body bg-gray-100">
      <p class="text-xs mb-2"><b>Details: </b>#{!! $row->description !!}</p>
      <span class="text-xs d-block mb-2">Personal Note:
        <span class="text-dark fw-bold ms-sm-2">{{ $row->note }}</span>
      </span>

      @if($row->document)
        <a href="{{ asset($row->document) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
          View Document
        </a>
      @else
        <span class="text-danger">No Document</span>
      @endif
    </div>
  </div>
</div>




                @endforeach
              </div>





            </ul>
          </div>
        </div>
      </div>

      
      <div class="col-md-6 mt-4">
        <div class="card h-100 mb-4">
          <div class="card-header pb-0 px-3">
            <div class="row">
              <div class="col-md-6">
                <h6 class="mb-0">Add New Thinking</h6>
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
    
    
            <form  method="POST" action="{{ route('user.discussionStore') }}" id="postForm" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                  <div class="col-md-12 d-none">
                    <label>Date</label>
                    <div class="input-group mb-4">
                      <input id="date" type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ date('Y-m-d') }}" required autocomplete="date">
                        @error('date')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>


                </div>



                  <div class="col-md-12">
                    <label>Details</label>
                    <div class="input-group">
                      <textarea name="description" id="description" cols="30" rows="2" class="form-control summernote @error('description') is-invalid @enderror"></textarea>
                      @error('description')
                        <p class="text-danger">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  



                  <div class="col-md-12">
                    <label>Personal Note</label>
                    <div class="input-group">
                      <input id="note" type="text" class="form-control @error('note') is-invalid @enderror" name="note" >
                        @error('note')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>

                  <div class="col-md-12">
                    <label>Summery</label>
                    <div class="input-group">
                      <textarea name="summery"  id="summery" cols="30" rows="2" class="form-control @error('summery') is-invalid @enderror"></textarea>
                        @error('summery')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>


                  <div class="col-md-12">
                    <label>Person with you when discuss.</label>
                    <div class="input-group">
                      <textarea name="member"  id="member" cols="30" rows="2" class="form-control @error('member') is-invalid @enderror"></textarea>
                        @error('member')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>

                  
                  <div class="col-md-12">
                    <label>Document (<span style="color: red">maximum: 1mb</span>)</label>
                    <div class="input-group">
                      <input id="document" type="file" class="form-control" name="document">
                    </div>
                  </div>


                <div class="row">
                  <div class="col-md-12 mt-3">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
<script>
  $(function() {
      $( "form" ).submit(function() {
          
        $(".btn-submit").prepend('<i class="fa fa-spinner fa-spin"></i>');
        $(".btn-submit").attr("disabled", 'disabled');

      });
  });
  </script>

  
<script>
  $(document).ready(function() {
    $('.summernote').summernote({
      height: 120
    });
  });
</script>

@endsection