<?php
//connexion à la bdd
$servername = "localhost";
$username = "Amel"; 
$password = "stud"; 
$dbname = "projet_reseaux";
$conn = new mysqli($servername, $username, $password, $dbname);

//vérification si tous les paramètres sont fournis
if (isset($_POST['action_type'], $_POST['action_details'], $_POST['user'], $_POST['action_time'])) {
    $action_type = $_POST['action_type'];
    $action_details = $_POST['action_details'];
    $user = $_POST['user'];
    $action_time = $_POST['action_time'];

    $action_type = $conn->real_escape_string($action_type);
    $action_details = $conn->real_escape_string($action_details);
    $user = $conn->real_escape_string($user);
    $action_time = $conn->real_escape_string($action_time);

    //préparer et lier
    $stmt = $conn->prepare("INSERT INTO historique (action_type, action_details, user, action_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $action_type, $action_details, $user, $action_time);

    //exécuter la requête
    if ($stmt->execute()) {
        echo "Action enregistrée avec succès!<br>";
    } else {
        echo "Erreur: " . $stmt->error . "<br>";
    }
    $stmt->close();
} else {
    echo "Erreur : Paramètres manquants.<br>";
}

$conn->close();
?>
