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
    text-transform: uppercase;
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
	 padding: 6px;
	 font-family: sans-serif;
	 border-right: 1px solid #fff;
}
 .wrapper .tableData table tr {
	 border-bottom: 1px solid #ebebeb;
}
 .wrapper .tableData table tr td {
	 padding: 6px;
	 color: #625f5f;
	 text-transform: capitalize;
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
                @if (isset($user))
                    
                {{$user->name}} {{$user->surname}}<br>
                Email: {{$user->email}}<br>
                Phone: {{$user->phone}}<br>
                    
                @else

                <p>All depositors report</p>
                    
                @endif
            </div>
        </div>

        <div class="tableData">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Note</th>
                        <th>Last Digit</th>
                        <th>Fine</th>
                        <th>Amount </th>
                    </tr>
                </thead>
            <tbody>  
                @foreach ($data as $data)
                <tr>
                    <td>{{$data->date}}</td>
                    <td>{{$data->note}}</td>
                    <td>{{$data->last_digit}}</td>
                    <td>{{$data->fine}}</td>
                    <td>{{$data->amount}}</td>
                 </tr> 
                @endforeach    
            </tbody>
        </table>
        </div>

    </div>

</body>
</html>