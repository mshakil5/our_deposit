@extends('admin.layouts.admin')

@section('content')

<input type="hidden" id="userID" value="{{ $id ?? ''}}">
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Transaction</h3>
                    </div>
                    <div class="card-body">
                        <table id="allTran" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Tran. id</th>
                                    <th>Name/Phone</th>
                                    <th>Last Digit</th>
                                    <th>Note</th>
                                    <th>Document</th>
                                    <th>Amount</th>
                                    <th>Fine</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>

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
                    } else {
                        showError('Failed to update status.');
                    }
                },
                error: function(xhr, status, error) {
                    showError('An error occurred. Please try again.');
                }
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


<script>
$(document).ready(function() {

    var id = $('#userID').val();
    var ajaxUrl = "{{ url('/admin/transaction/data') }}/" + (id ? id : "");

    $('#allTran').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50, // 🔥 Show 50 items per page

        ajax: ajaxUrl,

        dom: 'Bfrtip', // 🔥 Enable Buttons
        buttons: [
            'copyHtml5',
            'csvHtml5',
            'excelHtml5',
            'pdfHtml5',
            'print'
        ],

        order: [],

        columnDefs: [
            { targets: 0, orderable: false },
            { targets: [3,6,9], orderable: false }
        ],

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
            { data: 'date', name: 'date' },
            { data: 'tranid', name: 'tranid' },
            { data: 'username', name: 'username' },
            { data: 'last_digit', name: 'last_digit' },
            { data: 'note', name: 'note' },
            { data: 'document', name: 'document' },
            { data: 'amount', name: 'amount' },
            { data: 'fine', name: 'fine' },
            { data: 'status_switch', name: 'status_switch', orderable: false }
        ]
    });


});

</script>

@endsection