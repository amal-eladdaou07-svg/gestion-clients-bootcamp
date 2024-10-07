<?php
require_once 'db.php';  // Connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Vérification de l'existence de l'email
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Générer un token de réinitialisation
        $token = bin2hex(random_bytes(50));  // Génère un token sécurisé
        $expiration = date("Y-m-d H:i:s", strtotime('+1 hour'));  // Expire dans 1 heure

        // Sauvegarder le token dans la base de données
        $insert_stmt = $conn->prepare("INSERT INTO mot_de_passe_tokens (utilisateur_id, token, date_expiration) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iss", $user['id'], $token, $expiration);
        $insert_stmt->execute();

        // Envoyer un email avec le lien de réinitialisation
        $reset_link = "http://votre_site/reset_password.php?token=" . $token;
        $to = $email;
        $subject = "Réinitialisation de votre mot de passe";
        $message = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : " . $reset_link;
        $headers = "From: noreply@votre_site.com";

        mail($to, $subject, $message, $headers);
        echo "Un lien de réinitialisation a été envoyé à votre adresse email.";
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }
}
?>
