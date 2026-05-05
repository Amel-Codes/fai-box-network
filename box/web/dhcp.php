<?php
//Appel à saveBdd.php pour sauvegarder les actions en historique
function log_action($type, $details, $user, $time) {
    $command = "curl -X POST -d \"action_type=$type&action_details=$details&user=$user&action_time=$time\" http://192.168.1.1/saveBdd.php";
    $output = shell_exec($command);
    return $output;
}

//fonction pour activer/désactiver le service DHCP
function toggle_dhcp($action) {
    if ($action == 'start') {
        shell_exec('sudo systemctl start isc-dhcp-server');
    } elseif ($action == 'stop') {
        shell_exec('sudo systemctl stop isc-dhcp-server');
    }
}

function display_dhcp_range() {
    $output = shell_exec('sudo /home/stud/DHCP/affich_plage.sh');
    return $output;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = get_current_user();
    $action_time = date('Y-m-d H:i:s');
    if (isset($_POST['toggle_dhcp'])) {
        $action_type = $_POST['toggle_dhcp'];
        $message = $_POST['toggle_dhcp'];
        toggle_dhcp($_POST['toggle_dhcp']);
    } elseif (isset($_POST['reset_range'])) {
       $action_type = "Delete DHCP config.";
       $message = shell_exec("sudo /home/stud/DHCP/delete_Config_DHCP.sh 2>&1");
    } elseif (isset($_POST['add_range_standard'])) {
        $range = intval($_POST['standard_range']);
        $action_type = "Add stand. range.";
        $message =shell_exec("sudo /home/stud/DHCP/DHCP_Clients.sh $range 2>&1");
    } elseif (isset($_POST['add_range_advanced'])) {
        $start_ip = $_POST['start_ip'];
        $end_ip = $_POST['end_ip'];
        $action_type = "Add advanc. range.";
        $message =shell_exec("sudo /home/stud/DHCP/DHCP_ClientsAvancé.sh $start_ip $end_ip 2>&1");
    } elseif (isset($_POST['modif_lease_time'])) {
        $NbSecondes = intval($_POST['NbSecondes']);
        $action_type = "Modify leaseTime.";
        $message =shell_exec("sudo /home/stud/DHCP/modif_bail.sh $NbSecondes 2>&1");
    }
    if (isset($action_type)) {
        log_action($action_type, $message, $user, $action_time);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DHCP Configuration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #0078d7;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        .container {
            margin: 2rem auto;
            max-width: 800px;
            padding: 1rem;
        }
        .section {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .section h3 {
            margin-top: 0;
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
        input[type="text"], input[type="number"] {
            padding: 8px;
            width: 150px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .form-group {
            margin-bottom: 15px;
        }

        .notification {
            padding: 10px 20px;
            margin: 10px 0;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .success {
            background-color: #4CAF50; /*vert*/
        }
        .error {
            background-color: #f44336; /*rouge*/
        }
    </style>
    <script>
      setTimeout(() => {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.style.opacity = '0'; //transition progressive
            setTimeout(() => {
                notification.style.display = 'none'; //supp complètement après transition
            }, 50); //50 ms supp pour q la transition s'applique
        }
      }, 4000); //4.5s
    </script>
</head>
<body>

<header>
    <h1>Configuration DHCP</h1>
</header>
<div class="container">
    <!-- Notifications -->
    <?php if (!empty($message)) : ?>
        <div id="notification" class="notification <?= strpos($message, 'Erreur') !== false ? 'error' : 'success' ?>">
            <?= nl2br(htmlspecialchars($message)) ?>
        </div>
    <?php endif; ?>

    <!--section pour Activer/Désactiver le DHCP -->
    <div class="section">
        <h3>Activer/Désactiver le DHCP</h3>
        <form method="POST">
            <?php
            $dhcp_status = shell_exec('systemctl is-active isc-dhcp-server');
            $dhcp_status = trim($dhcp_status);
            ?>
            <button type="submit" name="toggle_dhcp" value="<?= $dhcp_status == 'active' ? 'stop' : 'start' ?>" class="btn">
                <?= $dhcp_status == 'active' ? 'Désactiver' : 'Activer' ?> le DHCP
            </button>
        </form>
    </div>

    <!--section pour Afficher la Plage d'Adresses DHCP -->
    <div class="section">
        <h3>Plage d'adresses DHCP</h3>
        <p><?= display_dhcp_range(); ?></p>
    </div>

    <!--section pour Configurer la Plage d'Adresses DHCP -->
    <div class="section">
        <h3>Configurer la Plage (Standard)</h3>
        <form method="POST">
            <div class="form-group">
                <label for="standard_range">Taille de la plage (nombre d'adresses) :</label>
                <input type="number" name="standard_range" id="standard_range" required placeholder="ex:10">
            </div>
            <button type="submit" name="add_range_standard" class="btn">Ajouter la Plage</button>
        </form>
    </div>

    <!--section pour Configurer la Plage d'Adresses DHCP (Mode Avancé) -->
    <div class="section">
        <h3>Configurer la Plage (Mode Avancé)</h3>
        <form method="POST">
            <div class="form-group">
                <label for="start_ip">IP de début :</label>
                <input type="text" name="start_ip" id="start_ip" required placeholder="  xxx.xxx.xxx.xxx">
            </div>
            <div class="form-group">
                <label for="end_ip">IP de fin :</label>
                <input type="text" name="end_ip" id="end_ip" required placeholder="  xxx.xxx.xxx.xxx">
            </div>
            <button type="submit" name="add_range_advanced" class="btn">Ajouter la Plage</button>
        </form>
    </div>

    <!--section pour modifier le bail -->
    <div class="section">
        <h3>Modifier le bail</h3>
        <form method="POST">
            <div class="form-group">
                <label for="NbSecondes">Bail pour les nouvelles connexions (en secondes) :</label>
                <input type="number" name="NbSecondes" id="NbSecondes" required placeholder=" 86400 (1 day) recommandé">
            </div>
            <button type="submit" name=" modif_lease_time" class="btn">Appliquer le nouveau bail</button>
        </form>
    </div>

    <!--section pour Supprimer la Configuration -->
    <div class="section">
        <h3>Réinitialiser la Configuration</h3>
        <form method="POST">
            <button type="submit" name="reset_range" class="btn" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser la configuration actuelle ?\n\n Vous perderez la connectivité sans une nouvelle configuration.');">
                Réinitialiser la Configuration
            </button>
        </form>
    </div>
</div>
</body>
</html>
