@extends('admin.layouts.admin')

@section('content')


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
                                    <th>User</th>
                                    @foreach ($months as $month)
                                        <th>{{ $month }}</th>
                                    @endforeach
                                    <th>Total</th> <!-- Column for row-wise sums -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        @foreach ($months as $month)
                                            <td>
                                                @if ($report[$month][$user->id]['deposited'])
                                                    Deposited ({{ number_format($report[$month][$user->id]['amount'], 2) }})
                                                @else
                                                    <span class="badge bg-danger">Not Deposited</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>{{ number_format($rowSums[$user->id], 2) }}</td> <!-- Row-wise sum -->
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th> <!-- Label for column-wise sums -->
                                    @foreach ($months as $month)
                                        <th>{{ number_format($columnSums[$month], 2) }}</th> <!-- Column-wise sum -->
                                    @endforeach
                                    <th>{{ number_format(array_sum($columnSums), 2) }}</th> <!-- Grand total -->
                                </tr>
                            </tfoot>
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


        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 20,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            
        });

        
    });
</script>
@endsection