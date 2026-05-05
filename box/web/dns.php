<?php
//Appel à saveBdd.php pour sauvegarder les actions en historique
function log_action($action_type, $action_details, $user, $action_time) {
    $command = "curl -X POST -d \"action_type=$action_type&action_details=$action_details&user=$user&action_time=$action_time\" http://192.168.1.1/saveBdd.php";
    $output = shell_exec($command);
    return $output;
}
// afficher les enregistremets sans bouton
function display_records() {
    $output = shell_exec("sudo /home/stud/DNS/list_records.sh 2>&1");
    return $output;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $user = get_current_user();
   $action_time = date('Y-m-d H:i:s');

   $record = $_POST['record'] ?? '';
   $type = $_POST['type'] ?? '';

   if (isset($_POST['delete_record'])) {
       $action_type = "delete_record";
       $message = shell_exec("sudo /home/stud/DNS/delete_record.sh $type $record 2>&1");
    } elseif (isset($_POST['add_record_standard'])) {
        $action_type = "add_record_standard";
        $message =shell_exec("sudo /home/stud/DNS/add_record.sh  'A' $record 2>&1");
    } elseif (isset($_POST['add_record_advanced'])) {
        $action_type = "add_record_advanced";
        $message =shell_exec("sudo /home/stud/DNS/add_record.sh $type $record 2>&1");
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
    <title>DNS Configuration</title>
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
            width: 200px;
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
            notification.style.opacity = '0'; // Transition progressive
            setTimeout(() => {
                notification.style.display = 'none'; // Supp complètement après transition
            }, 500); // 500 ms supp pour q la transition s'applique
        }
      }, 3000); //3s
    </script>

</head>
<body>
<header>
    <h1>Configuration DNS</h1>
</header>
<div class="container">
    <!--notifications -->
    <?php if (!empty($message)) : ?>
        <div id= "notification" class="notification <?= strpos($message, 'Erreur') !== false ? 'error' : 'success' ?>">
            <?= nl2br(htmlspecialchars($message)) ?>
        </div>
    <?php endif; ?>
  
    <!--section pour ajouter un enregistrement (sous domaine ) (Mode facile) -->
    <div class="section">
        <h3>Ajouter un nouveau sous-domaine à amel.com (Standard)</h3>
        <form method="POST">
            <div class="form-group">
                <label for="record">Nom du sous domaine :</label>
                <input type="text" name="record" id="record" required>
            </div>
            <button type="submit" name="add_record_standard" class="btn">Créer le sous-domaine</button>
        </form>
    </div>

    <!--section pour ajouter un enregistrement (sous domaine )  (Mode Avancé) -->
    <div class="section">
        <h3>Ajouter un nouveau sous-domaine à amel.com (Mode Avancé)</h3>
        <form method="POST">
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" name="type" id="type" required placeholder="A ou MX ou CNAME">
            </div>
            <div class="form-group">
                <label for="record">Nom:</label>
                <input type="text" name="record" id="record" required>
            </div>
            <button type="submit" name="add_record_advanced" class="btn">Créer le sous-domaine</button>
        </form>
    </div>
    
    <!--section pour Afficher les sous domaines existants -->
    <div class="section">
        <h3>Enregistrements actuels</h3>
        <p><?= display_records(); ?> </p>
    </div>

    <!--section pour Supprimer Un ss dmn -->
    <div class="section">
        <h3>Supprimer un sous-domaine</h3>
        <form method="POST">
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" name="type" id="type" required required placeholder="A ou MX ou CNAME">
            </div>
            <div class="form-group">
                <label for="record">Nom :</label>
                <input type="text" name="record" id="record" required>
            </div>
            <button type="submit" name="delete_record" class="btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet enegistrement?');">
            Supprimer
            </button>
        </form>
    </div>
</div>
</body>
</html>
