@extends('user.layouts.user')
  
@section('content')


  
<div class="container-fluid py-4">
    <div class="row">
      
      <div class="col-12 col-xl-4">
        <div class="card h-100">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-md-8 d-flex align-items-center">
                <h6 class="mb-0">Profile Information</h6>
              </div>
              <div class="col-md-4 text-end">
                
              </div>
            </div>
          </div>
          <div class="card-body p-3">
            
            <div class="position-relative w-100">
              @if (Auth::user()->profileimage)
              <img src="{{ asset( Auth::user()->profileimage) }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
              @else
              <img src="{{ asset('frontend/assets/img/banner.jpg') }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
              @endif

            </div>

            <hr class="horizontal gray-light my-1">
            <ul class="list-group">
              <li class="border-0 ps-0 pt-0 text-sm" style="display: block;"><strong class="text-dark">Full Name:</strong> &nbsp; {{ Auth::user()->name }}</li>
              <li class=" border-0 ps-0 text-sm" style="display: block;"><strong class="text-dark">Mobile:</strong> &nbsp; {{ Auth::user()->phone }}</li>
              <li class=" border-0 ps-0 text-sm" style="display: block;"><strong class="text-dark">Email:</strong> &nbsp; {{ Auth::user()->email }}</li>
              <li class=" border-0 ps-0 text-sm" style="display: block;"><strong class="text-dark">Total Deposit:</strong> &nbsp; <b>{{$totalDeposit}} TK</b></li>
              <li class=" border-0 ps-0 text-sm" style="display: block;"><strong class="text-dark">Total Fine:</strong> &nbsp; <b>{{\App\Models\Transaction::where('user_id', Auth::user()->id)->where('status', 1)->sum('fine')}} TK</b></li>

            </ul>
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-8 mt-4">
        <div class="card h-100">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-md-8 d-flex align-items-center">
                <h6 class="mb-0">Profile Edit</h6>
              </div>
              <div class="col-md-4 text-end">
                <a href="javascript:;">
                  <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Profile"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="card-body p-3">

            <hr class="horizontal gray-light ">
            
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
    
    
            <form  method="POST" action="{{ route('user.profileUpdate') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                  <div class="col-md-6">
                    <label>First Name</label>
                    <div class="input-group mb-4">
                      <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ Auth::user()->name }}" required autocomplete="name" autofocus placeholder="Full Name">
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label>Phone</label>
                    <div class="input-group mb-4">
                      <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ Auth::user()->phone }}" required autocomplete="phone" autofocus placeholder="Phone">
                        @error('phone')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label>Profile Image</label>
                    <div class="input-group mb-4">
                      <input id="profileimage" type="file" class="form-control" name="profileimage">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label>Cover Image</label>
                    <div class="input-group mb-4">
                      <input id="coverimage" type="file" class="form-control" name="coverimage">
                    </div>
                  </div>

                </div>
                <div class="mb-4">
                  <label>Email Address</label>
                  <div class="input-group">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ Auth::user()->email }}" required autocomplete="email" placeholder="Email">
                  </div>
                    @error('email')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label>Password</label>
                    <div class="input-group mb-4">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" placeholder="Password">
                    </div>
                    @error('password')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="col-md-6 ps-2 mb-3">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" placeholder="Confirm Password">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <button type="submit" class="btn bg-gradient-dark w-100">Update</button>
                  </div>
                </div>
                
            </form>
          </div>
        </div>
      </div>
      
      <div class="col-12 mt-4">
        <div class="card mb-4">
          <div class="card-header pb-0 p-3">
            <h6 class="mb-1">My Installments</h6>
          </div>
          <div class="card-body p-3">
            <div class="row">

              @foreach ($trans as $key => $tran)
                  
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">


                  <div class="position-relative">
                    <a class="d-block shadow-xl border-radius-xl" href="{{ asset($tran->document) }}">
                      <img src="{{ asset($tran->document) }}" alt="img-blur-shadow" class="img-fluid shadow border-radius-xl">
                    </a>
                  </div>
                  <div class="card-body px-1 pb-0">
                    <p class="text-gradient text-dark mb-2 text-sm">Installment #PMK00{{$key+1}}</p>
                    <a href="javascript:;">
                      <h5>
                        ৳{{$tran->amount}}
                      </h5>
                    </a>
                    
                    <p class="text-sm">
                      @if ($tran->fine)
                      <b>Fine: </b>{{$tran->fine}} <br>
                      @endif
                      <b>Last digit: </b>{{$tran->last_digit}} <br>
                      <b>Transaction ID: </b>{{$tran->tranid}} <br>
                      <b>Note: </b>{{$tran->note}}
                    </p>

                    <div class="d-flex align-items-center justify-content-between">
                      {{-- <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button> --}}
                    </div>
                  </div>


                </div>
              </div>
              @endforeach
              

              {{-- <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card h-100 card-plain border">
                  <div class="card-body d-flex flex-column justify-content-center text-center">
                    <a href="javascript:;">
                      <i class="fa fa-plus text-secondary mb-3"></i>
                      <h5 class=" text-secondary"> New project </h5>
                    </a>
                  </div>
                </div>
              </div> --}}
            </div>
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