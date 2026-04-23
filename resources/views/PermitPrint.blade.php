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
            margin-bottom: 5px;
        }
        p {
            text-align: center;
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Sanitary Permit Report - Quarter {{ $quarter }}</h2>

    <p>
        Period: {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} 
        to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}
    </p>

    <!-- ✅ Show Filter Option -->
    <p>
        Filter: 
        @if(isset($option) && $option == 'with')
            With Sanitary Code
        @elseif(isset($option) && $option == 'without')
            Without Sanitary Code
        @else
            All Records
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Business Name</th>
                <th>Address</th>
                <th>Sanitary Code</th> <!-- ✅ NEW COLUMN -->
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $record->name_of_establishment }}</td>
                    <td>{{ $record->barangay }}</td>
                    
                    <!-- ✅ Handle empty/null -->
                    <td class="text-center">
                        {{ $record->sanitary_code ?: 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>