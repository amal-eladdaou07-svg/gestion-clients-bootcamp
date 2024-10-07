<?php include 'check_login.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://www.bootcamp.fr/wp-content/uploads/2024/02/cropped-bootcampfavicon-192x192.png" sizes="192x192" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script>
    <title>Gestion des Clients</title>
    <style>
      .parallax {
    background-image: url(images/Default_an_image_that_combines_all_the_formations_within_Bootc_0.jpg);
    height: 520px;
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}
       
        .logout-button {
    float: right;
    margin-top: 10px;
    padding: 10px 20px;
    background-color: #ffffff;  /* Couleur de fond du bouton */
    color: #000000;  /* Couleur du texte */
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.logout-button:hover {
    background-color: #ff0000;  /* Couleur de fond au survol */
}

    </style>
</head>
<body>
    <div class="parallax"></div>
    <div class="content">
        <header>
            <img src="images/bootcamp_new.png" alt="Bootcamp Image" alt="Bootcamp Formation" style="text-align: left;">
            <h1><i class="fas fa-user-tie"></i> Gestion des Clients</h1>
            <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
        </header>
        <div class="card">
            <div class="buttons">
                <button onclick="window.location.href='ajouter.html'"><i class="fas fa-plus"></i> Ajouter</button>
                <button onclick="window.location.href='rechercher.html'"><i class="fas fa-search"></i> Chercher</button>
                <button onclick="window.location.href='Lister.php'"><i class="fas fa-list"></i> Lister</button>
                <button onclick="window.location.href='statistiques.html'"><i class="fas fa-chart-pie"></i> Statistiques</button>
            </div>
        </div>
        <button onclick="retourAccueil()" id="btn-accueil" style="display: none;"><i class="fas fa-home"></i> Accueil</button>
    </div>
</body>
</html>
