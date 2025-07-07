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
                  <span class="text-xs"><b>Amount: </b>{{$tran->amount}} <br> <b>Fine: </b>{{$tran->due}} </span>
                  <span class="text-xs">Date: <span class="text-dark ms-sm-2 font-weight-bold">{{$tran->date}}</span></span>
                  <span class="text-xs">Last Digit: <span class="text-dark ms-sm-2 font-weight-bold">{{$tran->last_digit}}</span></span>
                  <span class="text-xs">Note: <span class="text-dark font-weight-bold ms-sm-2">{{$tran->note}}</span></span>
                </div>
                <div class="ms-auto text-end">
                  <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{route('user.installmentDelete', $tran->id)}}"><i class="far fa-trash-alt me-2"></i>Delete</a>
                  {{-- <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a> --}}
                </div>
              </li>
              @endforeach
              



            </ul>
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

@endsection