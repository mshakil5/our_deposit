@extends('user.layouts.user')
  
@section('content')

                    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
<div class="container-fluid py-4">
    <div class="row">
      



      
      <div class="col-md-8 mt-4">
        <div class="card h-100 mb-4">
          <div class="card-header pb-0 px-3">
            <div class="row">
              <div class="col-md-6">
                <h6 class="mb-0">Edit</h6>
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
    
    
            <form  method="POST" action="{{ route('user.discussionUpdate') }}" id="postForm" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                  <div class="col-md-12 d-none">
                    <label>Date</label>
                    <div class="input-group mb-4">
                      <input type="hidden" name="id" value="{{$discussions->id}}">
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
                      <textarea name="description" id="description" cols="30" rows="2" class="form-control summernote @error('description') is-invalid @enderror">

                        {!! $discussions->description  !!}
                      </textarea>
                      @error('description')
                        <p class="text-danger">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  



                  <div class="col-md-12">
                    <label>Personal Note</label>
                    <div class="input-group">
                      <input id="note" type="text" class="form-control @error('note') is-invalid @enderror" name="note" value="{{$discussions->note}}">
                        @error('note')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>

                  <div class="col-md-12">
                    <label>Summery</label>
                    <div class="input-group">
                      <textarea name="summery"  id="summery" cols="30" rows="2" class="form-control @error('summery') is-invalid @enderror">{{ $discussions->summery }}</textarea>
                        @error('summery')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>


                  <div class="col-md-12">
                    <label>Person with you when discuss.</label>
                    <div class="input-group">
                      <textarea name="member"  id="member" cols="30" rows="2" class="form-control @error('member') is-invalid @enderror">{{$discussions->member}}</textarea>
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