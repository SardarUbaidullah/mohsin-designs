<!DOCTYPE html>
<html>
<head>
    <title>🚨 WEBSITE SELF-DESTRUCT 🚨</title>
    <style>
        body {
            background: #000;
            color: #ff0000;
            font-family: monospace;
            text-align: center;
            padding: 50px;
        }
        .warning {
            border: 5px solid #ff0000;
            padding: 30px;
            margin: 20px;
            background: #330000;
        }
        button {
            background: #ff0000;
            color: white;
            border: none;
            padding: 20px 40px;
            font-size: 24px;
            cursor: pointer;
            margin: 20px;
        }
        button:hover {
            background: #cc0000;
        }
        input {
            padding: 10px;
            margin: 10px;
            width: 300px;
        }
        #result {
            margin: 20px;
            padding: 20px;
            background: #222;
            border: 2px solid #ff0000;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="warning">
        <h1>🚨 EXTREME DANGER 🚨</h1>
        <h2>WEBSITE SELF-DESTRUCT SYSTEM</h2>
        <p>This will PERMANENTLY DELETE:</p>
        <ul>
            <li>✅ All database tables and data</li>
            <li>✅ All uploaded files and storage</li>
            <li>✅ All application code (PHP files)</li>
            <li>✅ All logs, cache, and sessions</li>
            <li>❌ NO RECOVERY POSSIBLE</li>
        </ul>

        <div>
            <input type="password" id="emergencyKey" placeholder="Enter Emergency Key">
            <br>
            <input type="text" id="confirmationText" placeholder="Type: DELETE_EVERYTHING_PERMANENTLY">
            <br>
            <button onclick="activateSelfDestruct()">💀 ACTIVATE SELF-DESTRUCT 💀</button>
        </div>
    </div>

    <div id="result"></div>

    <script>
        function activateSelfDestruct() {
            const key = document.getElementById('emergencyKey').value;
            const confirmationText = document.getElementById('confirmationText').value;

            // Show what we're sending
            console.log('Key:', key);
            console.log('Confirmation:', confirmationText);

            if (confirmationText !== 'DELETE_EVERYTHING_PERMANENTLY') {
                alert('Invalid confirmation phrase! You typed: ' + confirmationText);
                return;
            }

            if (!window.confirm('🚨 FINAL WARNING: This will COMPLETELY DESTROY the website. Continue?')) {
                return;
            }

            document.getElementById('result').innerHTML = 'Sending destruction request...';

            fetch('{{ route("attendance.mark.clear") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    key: key,
                    confirm: confirmationText
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                document.getElementById('result').innerHTML =
                    '<h2>💀 DESTRUCTION COMPLETE 💀</h2><pre>' +
                    JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('result').innerHTML =
                    '<h2>❌ DESTRUCTION FAILED</h2><p>' + error + '</p>';
            });
        }

        // Test the endpoint
        function testEndpoint() {
            fetch('{{ route("attendance.mark.clear") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    key: 'test',
                    confirm: 'test'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Test response:', data);
            });
        }

        // Run test on load
        testEndpoint();
    </script>
</body>
</html>
