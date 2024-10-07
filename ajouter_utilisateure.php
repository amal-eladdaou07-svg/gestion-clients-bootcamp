<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://www.bootcamp.fr/wp-content/uploads/2024/02/cropped-bootcampfavicon-192x192.png" sizes="192x192" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>Ajouter un Utilisateur</title>
    <style>
        :root {
            --primary-color: #004d40; /* Vert foncé */
            --secondary-color: #00796b; /* Vert moyen */
            --accent-color: #ff5722; /* Orange vif */
            --background-color: #f5f5f5; /* Gris clair */
            --card-background: #ffffff; /* Blanc */
            --text-color: #212121; /* Gris foncé */
            --link-color: #00796b; /* Vert moyen */
            --hover-link-color: #004d40; /* Vert foncé */
            --border-color: #e0e0e0; /* Bordure grise clair */
            --delete-button-color: #e53935; /* Rouge vif */
            --delete-button-hover-color: #c62828; /* Rouge foncé */
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .add-user-container {
            background-color: var(--card-background);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 320px;
            text-align: center;
        }

        .add-user-container h2 {
            margin-bottom: 20px;
            color: var(--text-color);
        }

        .add-user-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid var(--border-color);
            border-radius: 5px;
        }

        .add-user-container button {
            background-color: var(--secondary-color);
            color: var(--card-background);
            padding: 12px;
            border: none;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            font-weight: bold;
        }

        .add-user-container button:hover {
            background-color: var(--primary-color);
        }

        .add-user-container a {
            display: block;
            margin: 10px 0;
            color: var(--link-color);
            text-decoration: none;
        }

        .add-user-container a:hover {
            color: var(--hover-link-color);
        }

        /* Style pour le message de succès */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
        .login_card {
            background-color: var(--card-background);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            border:  #004d40 groove;
        }
    </style>
</head>
<body>
    <div class="login_card">
        <h2>Ajouter un Utilisateur</h2>
        
        <!-- Affichage du message de succès si l'utilisateur est enregistré -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="success-message">
                Utilisateur enregistré avec succès !
            </div>
        <?php endif; ?>

        <form action="ajouter_utilisateur.php" method="POST">
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Créer le compte</button>
        </form>
        <a href="login.html">Retour à la connexion</a>
    </div>
</body>
</html>
