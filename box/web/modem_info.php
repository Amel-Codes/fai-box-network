<?php
// Appeler les scripts shell pour récupérer les informations du système
$ip_info = shell_exec("/home/stud/MODEM/ip_info.sh");
list($private_ip, $public_ip) = explode("|", trim($ip_info));

$modem_info = shell_exec("/home/stud/MODEM/modem_info.sh");
list($mac_address, $uptime, $firmware_version) = explode("|", trim($modem_info));

$network_stats = shell_exec("/home/stud/MODEM/network_stats.sh");

//connexion à la base de données pour accéder à l'historique des actions
$conn = new mysqli("localhost", "Amel", "stud", "projet_reseaux");
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
$query = "SELECT * FROM historique";
$result = $conn->query($query);
if ($result === false) {
    die("Erreur dans la requête: " . $conn->error);
}
$historical_actions = [];
while ($row = $result->fetch_assoc()) {
    $historical_actions[] = $row;
}
$conn->close();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations du Modem</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #0078d7;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        .container {
            width: 80%;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .section {
            margin-bottom: 2rem;
        }
        .section h2 {
            background-color: #0078d7;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
        .info-box {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 10px 20px;
            background-color: #0078d7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056a3;
        }
        pre {
            background-color: #f7f7f7;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.9em;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<header>
    <h1>Informations du Modem</h1>
</header>

<div class="container">
    <!--section des informations Générales du Modem -->
    <div class="section">
        <h2>Informations Générales</h2>
        <div class="info-box">
            <p><strong>Adresses IP Privées :</strong> <?= $private_ip ?></p>
            <p><strong>Adresse IP Publique :</strong> <?= $public_ip ?></p>
            <p><strong>Adresse MAC du Modem :</strong> <?= $mac_address ?></p>
            <p><strong>Durée de Connexion :</strong> <?= $uptime ?></p>
            <p><strong>Version du Firmware :</strong> <?= $firmware_version ?></p>
        </div>
    </div>

    <!--section des informations Réseau -->
    <div class="section">
        <h2>Statistiques du Réseau</h2>
        <div class="info-box">
            <pre><?= nl2br($network_stats) ?></pre>
        </div>
    </div>

    <!--section des l'historique des Actions -->
    <div class="section">
        <h2>Historique des Actions</h2>
        <table>
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Détails</th>
                    <th>Utilisateur</th>
                    <th>Temps</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historical_actions as $action) : ?>
                    <tr>
                        <td><?= $action['action_type'] ?></td>
                        <td><?= $action['action_details'] ?></td>
                        <td><?= $action['user'] ?></td>
                        <td><?= $action['action_time'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!--section pour un bouton d actualisation-->
    <div style="text-align: center;">
        <button class="btn" onclick="window.location.reload();">Actualiser</button>
    </div>
</div>
</body>
</html>
