<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>RHU Report - {{ $rhu }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }


        *{
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <h2>HEALTH CARD RHU Report: {{ $rhu }}</h2>
    <p>From: {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}  
       To: {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>

       @php
             $i = 1;
       @endphp

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Barangay</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Place of Employment</th>
                <th>Designation</th>
                <th>Date of Issuance</th>
                <th>Date of Expiration</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $record->full_name }}</td>
                    <td>{{ $record->barangay }}</td>
                    <td>{{ $record->age }}</td>
                    <td>{{ $record->gender }}</td>
                    <td>{{ $record->place_of_employment }}</td>
                    <td>{{ $record->designation }}</td>
                    <td>{{ $record->date_of_issuance ? \Carbon\Carbon::parse($record->date_of_issuance)->format('Y-m-d') : '' }}</td>
                    <td>{{ $record->date_of_expiration ? \Carbon\Carbon::parse($record->date_of_expiration)->format('Y-m-d') : '' }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
