<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Box - Paramètres avancés</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #0078d7;
            color: white;
            text-align: center;
            padding: 2rem 0;
        }
        header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        header p {
            margin: 0.5rem 0 0;
            font-size: 1.2rem;
        }
        nav {
            background-color: #0056a3;
            padding: 1rem;
            text-align: center;
        }
        nav a {
            color: white;
            font-weight: bold;
            margin: 0 1rem;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        nav a:hover {
            background-color: #003d7c;
        }
        .container {
            max-width: 700px;
            margin: 2rem auto;
            padding: 1rem;
            text-align: center;
        }
        .settings-container {
            display: flex;
            /*flex-wrap: wrap;*/
            justify-content: center;
            gap: 8rem;
        }
        .setting {
            background-color: #003d7c;
            color: white;
            border-radius: 10px;
            padding: 2rem;
            width: 400px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin: 0 1rem;
        }
        .setting:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .setting-icon {
            width: 70px;
            height: 70px;
            margin-bottom: 1rem;
        }
        .setting-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 1rem;
        }
        footer {
            text-align: center;
            padding: 1rem 0;
            background-color: #f4f4f4;
            color: #777;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Paramètres avancés</h1>
        <p>Configurez vos options réseau</p>
    </header>

    <nav>
        <a href="index.html">Accueil</a>
        <a href="connected_devices.php">Appareils Connectés</a>
        <a href="DEBITT.php">Vérification Vitesse</a>
        <a href="#">Support</a>
    </nav>
    <div class="container">
        <h2>Gérez vos paramètres réseau</h2>
        <div class="settings-container">
            <!--paramètre 1 :DNS-->
            <div class="setting">
                <a href="DNS.php" style="text-decoration: none; color: inherit;">
                    <img src="dns.png" alt="Configuration DNS" class="setting-icon">
                    <div class="setting-title">Configurer les DNS</div>
                </a>
            </div>
            <!--paramètre 2 :DHCP-->
            <div class="setting">
                <a href="DHCP.php" style="text-decoration: none; color: inherit;">
                    <img src="dhcp.png" alt="Configuration DHCP" class="setting-icon">
                    <div class="setting-title">Configurer le DHCP</div>
                </a>
            </div>

            <!--Autres paramètres, à implémenter dans les prochaines phase (S6) -->
            <div class="setting">
                <a href="pageParamètres.php" style="text-decoration: none; color: inherit;">
                    <img src="settings.png" alt="Autres Paramètres" class="setting-icon">
                    <div class="setting-title">Autres paramètres</div>
                </a>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 Interface Box. Tous droits réservés.</p>
    </footer>
</body>
</html>
