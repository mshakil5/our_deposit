@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <!-- right column -->
            <div class="col-md-8">
                <!-- general form elements disabled -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Update transaction</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="date" name="date">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Last digit <span class="text-danger">*</span></label>
                                        <input type="text" id="last_digit" name="last_digit" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Amount <span class="text-danger">*</span></label>
                                        <input type="number" id="amount" name="amount" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Fine</label>
                                        <input type="number" id="fine" name="fine" class="form-control">
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="feature-img">Document</label>
                                        <input type="file" class="form-control-file" id="document" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Note </label>
                                        <input type="text" class="form-control" id="note" name="note" >
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>


                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Update">Update</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                    <!-- /.card-footer -->
                    <!-- /.card-body -->
                </div>
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->


<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Transaction</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Tran. id</th>
                                    <th>Name/Phone</th>
                                    <th>Last Digit</th>
                                    <th>Document</th>
                                    <th>Amount</th>
                                    <th>Fine</th>
                                    <th>Status</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $tran)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $tran->date }}</td>
                                    <td>{{ $tran->tranid }}</td>
                                    <td>{{ $tran->user->name }} <br> {{ $tran->user->phone }}</td>
                                    <td>{{ $tran->last_digit }}</td>
                                    <td>
                                        <a href="{{ asset($tran->document) }}" target="blank">
                                            <img src="{{ asset($tran->document) }}" id="myImg" alt="" style="max-width: 100px; width: 100%; height: auto;">
                                        </a>
                                        
                                    </td>
                                    <td>{{ $tran->amount }}</td>
                                    <td>{{ $tran->fine }}</td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $tran->id }}" data-id="{{ $tran->id }}" {{ $tran->status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchStatus{{ $tran->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="btn btn-app"  id="EditBtn" rid="{{$tran->id}}">
                                            <i class="fas fa-edit" style="color: #2196f3;font-size:16px;"></i> Edit
                                        </a>

                                        {{-- <a class="btn btn-app" id="deleteBtn" rid="{{ $tran->id }}">
                                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>Delete
                                        </a> --}}

                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<script>
    $(document).ready(function() {
    
        $("#addThisFormContainer").hide();
        $("#FormCloseBtn").click(function() {
            $("#addThisFormContainer").hide(200);
            clearform();
        });
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var url = "{{ URL::to('/admin/transaction-status') }}";

        $(document).on('change', '.toggle-status', function() {
            var tranId = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    tranId: tranId,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 200) {
                        showSuccess(response.message);
                        window.setTimeout(function(){location.reload()},2000)
                    } else {
                        showError('Failed to update status.');
                    }
                },
                error: function(xhr, status, error) {
                    showError('An error occurred. Please try again.');
                }
            });
        });

        
        var editurl = "{{ URL::to('/admin/transaction') }}";
        //Edit
        $("#contentContainer").on('click', '#EditBtn', function() {
            //alert("btn work");
            codeid = $(this).attr('rid');
            //console.log($codeid);
            info_url = editurl + '/' + codeid + '/edit';
            //console.log($info_url);
            $.get(info_url, {}, function(data) {
                $("#date").val(data.data.date);
                $("#fine").val(data.data.fine);
                $("#amount").val(data.data.amount);
                $("#last_digit").val(data.data.last_digit);
                $("#note").val(data.data.note);
                $("#codeid").val(data.data.id);
                $("#addThisFormContainer").show(300);
            });
        });
        //Edit  end


        var upurl = "{{URL::to('/admin/transaction-update')}}";
        // console.log(url);
        $("#addBtn").click(function() {
            //Update

                var requiredFields = ['#date', '#amount'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }

                var form_data = new FormData();
                form_data.append("date", $("#date").val());
                form_data.append("last_digit", $("#last_digit").val());
                form_data.append("amount", $("#amount").val());
                form_data.append("fine", $("#fine").val());
                form_data.append("note", $("#note").val());

                var featureImgInput = document.getElementById('document');
                if(featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("document", featureImgInput.files[0]);
                }

                form_data.append("codeid", $("#codeid").val());

                $.ajax({
                    url: upurl,
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        showSuccess('Data updated successfully.');
                        reloadPage(2000);
                    },
                    error: function(xhr, status, error) {
                        // error('An error occurred. Please try again.');
                        console.log(xhr.responseText);
                    }
                });
            //Update
        });



        function populateForm(data) {
            console.log(data);
            $("#date").val(data.date);
            $("#fine").val(data.fine);
            $("#amount").val(data.amount);
            $("#last_digit").val(data.last_digit);
            $("#note").val(data.note);
            $("#codeid").val(data.id);
            $("#addThisFormContainer").show(300);
        }

        function clearform() {
            $('#createThisForm')[0].reset();
        }


        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });

        $("#image").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>
@endsection