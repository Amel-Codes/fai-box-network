<?php
function log_action($type, $details, $user, $time) {
    $command = "curl -X POST -d \"action_type=$type&action_details=$details&user=$user&action_time=$time\" http://192.168.1.1/saveBdd.php";
    $output = shell_exec($command);
    return $output;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = get_current_user();
    $action_time = date('Y-m-d H:i:s');
    $message = ""; //pour stocker le msg de notification

    if (isset($_POST['download_speed'])) {
        $action_type = "download_speed";
        $message = shell_exec("sudo /home/stud/DEBIT/download_speed2.sh 2>&1");
        $section_result = "download";
    } elseif (isset($_POST['upload_speed'])) {
        $action_type = "upload_speed";
        $message = shell_exec("sudo /home/stud/DEBIT/upload_speed.sh 2>&1");
        $section_result = "upload";
    } elseif (isset($_POST['ping_latency'])) {
        $ip = $_POST['ip'];
        $action_type = "ping_latency";
        $message = shell_exec("sudo /home/stud/DEBIT/ping_latency.sh $ip 2>&1");
        $section_result = "ping";
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
    <title>DEBIT Configuration</title>
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
            position: relative;
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
        .loader {
            display: none;
            margin: 10px auto;
            text-align: center;
        }
        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #0078d7;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
            position: relative;
        }
        .spinner span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14px;
            font-weight: bold;
        }
        .result {
            margin-top: 10px;
            font-size: 16px;
            color: #333;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        function showLoader(sectionId) {
            //masquer le bouton et afficher le loader
            const section = document.getElementById(sectionId);
            section.querySelector('.btn').style.display = 'none';
            section.querySelector('.loader').style.display = 'block';
            //simuler un % dynamique dans le spinner
            const spinnerText = section.querySelector('.spinner span');
            let percentage = 0;
            const interval = setInterval(() => {
                percentage += 10;
                spinnerText.textContent = percentage + '%';
                if (percentage >= 100) clearInterval(interval);
            }, 300);
        }
    </script>
</head>

<body>
<header>
    <h1>Configuration DEBIT</h1>
</header>
<div class="container">
    <!--section pour afficher la vitesse de téléchargement -->
    <div class="section" id="download_section">
        <h3>Vitesse de téléchargement</h3>
         <form method="POST" onsubmit="showLoader('download_section')">
            <button type="submit" name="download_speed" class="btn">Commencer</button>
            <div class="loader">
                <div class="spinner"><span>0%</span></div>
            </div>
        </form>
        <?php if ($section_result == "download") : ?>
            <div class="result"><?= $message ?></div>
        <?php endif; ?>
    </div>
    
    <!--section pour afficher la vitesse d'envoi -->
    <div class="section" id="upload_section">
        <h3>Vitesse d'envoi</h3>
        <form method="POST" onsubmit="showLoader('upload_section')">
            <button type="submit" name="upload_speed" class="btn">Commencer</button>
            <div class="loader">
                <div class="spinner"><span>0%</span></div>
            </div>
        </form>
        <?php if ($section_result == "upload") : ?>
            <div class="result"> <?= $message ?></div>
        <?php endif; ?>
    </div>
    
    <!--section pour afficher la latence du ping -->
    <div class="section" id="ping_section">
        <h3>Latence du ping</h3>
        <form method="POST" onsubmit="showLoader('ping_section')">
            <div class="form-group">
                <label for="ip">IP à pinger :</label>
                <input type="text" name="ip" id="ip" required placeholder="       xxx.xxx.xxx.xxx">
            </div>
            <div><br><button type="submit" name="ping_latency" class="btn">Commencer</button></div>
            <div class="loader">
                <div class="spinner"><span>0%</span></div>
            </div>
        </form>
        <?php if ($section_result == "ping") : ?>
            <div class="result"> <?= $message ?></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
