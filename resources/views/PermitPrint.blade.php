<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sanitary Permit Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <h2>Sanitary Permit Report - Quarter {{ $quarter }}</h2>
    <p style="text-align: center;">
        Period: {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} 
        to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Business Name</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $record->name_of_establishment }}</td>
                    <td>{{ $record->barangay }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
