@php
use Illuminate\Support\Carbon;
use app\Models\Provoucher;
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMK</title>
   <style>
       @page { margin: 10px; }
    body { margin: 10px; }
 * {
	 margin: 0;
	 padding: 0;
}
 .wrapper {
	 max-width: 960px;
	 margin: 0 auto;
	 box-shadow: rgba(0, 0, 0, 0.04) -1px 2px 20px 15px;
	 padding: 25px;
}

.wrapper .heading {
	 margin-top: 50px;
}

 .wrapper .heading .title {
	 text-align: center;
	 font-weight: 600;
	 font-size: 2.1rem;
	 text-transform: uppercase;
	 font-family: monospace;
	 letter-spacing: 1px;
	 color: #436784;
}

.wrapper .heading .sm-title {
    text-align: center;
    font-weight: 200;
    font-size: 0.8rem;
}

 .wrapper .heading .donated {
	 color: #436784;
	 font-family: sans-serif;
}
 .wrapper .tableData {
	 margin-top: 50px;
	 margin-bottom: 100px;
	 min-width: 400px;
	 overflow-x: auto;
}
 .wrapper .tableData table {
	 width: 100%;
	 text-align: center;
	 border-collapse: collapse;
}
 .wrapper .tableData table tr th {
	 background-color: #436784;
	 color: azure;
	 padding: 4px;
	 font-family: sans-serif;
	 border-right: 1px solid #fff;
     font-size: 10px;
}
 .wrapper .tableData table tr {
	 border-bottom: 1px solid #ebebeb;
}
 .wrapper .tableData table tr td {
	 padding: 4px;
	 color: #625f5f;
     font-size: 10px;
}
 /* .wrapper .tableData table tr:nth-child(even) {
	 background: #436784 14;
} */
 
 
   </style>
</head>

<body>

    <div class="wrapper">
        <div class="heading">
            <div class="title">
                PMK
            </div>
            <div class="sm-title">
                <p>All depositors report</p>
            </div>
        </div>

        <div class="tableData">
            <table>
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Name</th>
                        <th>Email/Phone</th>
                        <th>Fine</th>
                        <th>Amount </th>
                    </tr>
                </thead>
            <tbody>  
                @php
                    $netFine = 0;
                    $netAmount = 0;
                @endphp
                @foreach ($data as $key => $data)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td style="text-align: left">{{$data->name}} {{$data->surname}}</td>
                    <td style="text-align: left">{{$data->email}} <br> {{$data->phone}}</td>
                    <td style="text-align: right">{{$data->total_fine}}</td>
                    <td style="text-align: right">{{$data->total_amount}}</td>
                 </tr> 
                 @php
                     $netFine = $netFine + $data->total_fine;
                     $netAmount = $netAmount + $data->total_amount;
                 @endphp
                @endforeach    
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th style="text-align: right">Total:</th>
                    <th style="text-align: right">{{$netFine}}</th>
                    <th style="text-align: right">{{$netAmount}}</th>
                </tr>
            </tfoot>
        </table>
        </div>

    </div>

</body>
</html>