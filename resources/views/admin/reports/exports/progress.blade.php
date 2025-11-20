<!DOCTYPE html>
<html>
<head>
    <title>Progress Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Progress Report</h1>
        <p>Generated on: {{ $date }}</p>
    </div>

    <div class="section">
        <h2>Overall Progress</h2>
        <table>
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Total Projects</td>
                <td>{{ $data['overall']['total_projects'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Completed Projects</td>
                <td>{{ $data['overall']['completed_projects'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>Completion Rate</td>
                <td>{{ $data['overall']['completion_rate'] ?? 0 }}%</td>
            </tr>
        </table>
    </div>
</body>
</html>
