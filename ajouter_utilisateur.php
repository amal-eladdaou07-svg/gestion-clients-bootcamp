<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    // Vérification que l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse e-mail non valide.";
        exit;
    }

    // Préparation de la requête d'insertion
    $sql = "INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $nom_utilisateur, $email, $mot_de_passe);

    if ($stmt->execute()) {
        // Redirection avec succès
        header('Location: ajouter_utilisateur.html?success=1');
        exit;
    } else {
        echo "Erreur lors de l'ajout de l'utilisateur.";
    }

    $stmt->close();
}

$conn->close();
?>
