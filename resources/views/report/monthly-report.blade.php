<!DOCTYPE html>
<html>
<head>
    <title>Monthly Deposit Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .not-deposited {
            background-color: #f8d7da;
            color: #721c24;
        }
        .total-row, .total-column {
            background-color: #d1ecf1;
            font-weight: bold;
        }
        /* Ensure table fits in landscape */
        th, td {
            min-width: 60px;
            max-width: 100px;
        }
        /* Page break for each year */
        .year-section {
            page-break-after: always;
        }
        .year-section:last-child {
            page-break-after: auto;
        }
    </style>
</head>
<body>
    @foreach ($years as $year)
        <div class="year-section">
            <h2>Monthly Deposit Report - {{ $year }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        @for ($month = 1; $month <= 12; $month++)
                            <th>{{ \DateTime::createFromFormat('!m', $month)->format('F') }}</th>
                        @endfor
                        <th class="total-column">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            @for ($month = 1; $month <= 12; $month++)
                                <td @if (!$report[$year][$month][$user->id]['deposited']) class="not-deposited" @endif>
                                    @if ($report[$year][$month][$user->id]['deposited'])
                                         {{ number_format($report[$year][$month][$user->id]['amount'], 2) }}
                                    @else
                                        Not Deposited
                                    @endif
                                </td>
                            @endfor
                            <td class="total-column">{{ number_format($rowSums[$year][$user->id], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th>Total</th>
                        @for ($month = 1; $month <= 12; $month++)
                            <th>{{ number_format($columnSums[$year][$month], 2) }}</th>
                        @endfor
                        <th>{{ number_format(array_sum($columnSums[$year]), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach
</body>
</html>