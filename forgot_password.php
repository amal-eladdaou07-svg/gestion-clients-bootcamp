<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: var(--card-background);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: --primary-color: #004d40 groove;
        }
        h2 {
    font-size: 24px; /* Taille de police légèrement réduite pour une hiérarchie visuelle raffinée */
    margin-bottom: 20px; /* Espacement sous le titre */
    text-align: center; /* Centrer le texte */
    color: var(--text-color); /* Couleur du texte */
    padding: 15px 25px; /* Espacement interne avec une largeur plus grande pour plus de confort */
    border-radius: 8px; /* Coins arrondis pour un aspect moderne */
    background: #ffffff; /* Fond blanc pour une apparence propre */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); /* Ombre subtile pour une profondeur élégante */
    font-weight: 500; /* Poids de la police pour une apparence plus professionnelle */
    border-bottom: 2px solid var(--primary-color); /* Bordure inférieure pour accentuer le titre */
    transition: all 0.3s ease-in-out; /* Transition douce pour les effets de survol */
}

h2:hover {
    color: var(--primary-color); /* Changer la couleur du texte au survol */
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25); /* Ombre plus prononcée au survol */
    transform: translateY(-2px); /* Légère élévation au survol */
    border-bottom: 2px solid var(--accent-color); /* Modifier la couleur de la bordure inférieure au survol */
}

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            box-sizing: border-box;
        }
       
        .options {
            text-align: center;
            margin-top: 20px;
        }
        .options a {
            color: var(--link-color);
            text-decoration: none;
        }
        .options a:hover {
            color: var(--hover-link-color);
            text-decoration: underline;
        }
        </style>
</head>
<body>
<div class="login-container" >
<img src="images/bootcamp_new.png" alt="Bootcamp Image" style="text-align: left;">
    <h2>Mot de passe oublié</h2>
    <form action="send_reset_link.php" method="POST">

        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Envoyer le lien de réinitialisation</button>
    </form>
    </div>
</body>
</html>
