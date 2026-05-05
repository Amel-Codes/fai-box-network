<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appareils Connectés</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 1em;
            text-align: left;
        }
        table th, table td {
            border: 1px solid #dddddd;
            padding: 8px;
        }
        table th {
            background-color: #0078d7;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Appareils Connectés</h1>
    <div>
        <?php
        // Exécution du script Bash
        $output = shell_exec('sudo /home/stud/connected_devices.sh');
        echo $output;
        ?>
    </div>
</body>
</html>
