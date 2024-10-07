<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Clients</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .alphabet-filter button.active {
    background-color: #ff5722;
    border: #000000 groove;
}

    </style>
</head>
<body>
    <header>
        <img src="images/bootcamp_new.png" alt="Bootcamp Image">
        <h1><i class="fas fa-user-tie"></i> Liste des Clients</h1>
    </header>
    <div class="container">
        <button onclick="retourAccueil()"><i class="fas fa-home"></i> Accueil</button>
        <h2>Liste des Clients</h2>
        <!-- Section de filtrage par session -->
        <div class="session-filter">
            <h3>Filtrer par Session</h3>
            <form id="session-filter-form">
                <label for="session-select">Sélectionner une session :</label>
                <select id="session-select" name="session_id" onchange="filtrerClients()">
                    <option value="">---Toutes les sessions---</option>
                    <?php
                    // Connexion à la base de données
                    $conn = new mysqli("localhost", "root", "", "amal");

                    // Vérifier la connexion
                    if ($conn->connect_error) {
                        die("La connexion a échoué : " . $conn->connect_error);
                    }

                    // Requête pour récupérer les sessions avec leurs dates de début et de fin
                    $sql = "SELECT id, session_date, date_fin FROM sessions";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Affichage des options pour les sessions
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["id"] . "'>" . htmlspecialchars($row["session_date"]) . " - " . htmlspecialchars($row["date_fin"]) . "</option>";
                        }
                    }

                    // Fermeture de la connexion
                    $conn->close();
                    ?>
                </select>
            </form>
        </div>

        <!-- Section de recherche par filtre alphabétique -->
        <h3>Filtrer par Lettre</h3>
        <div class="alphabet-container">
            <div class="alphabet-filter">
               
                <button onclick="filtrerClientsByLetter('')">Tous</button>
                <button onclick="filtrerClientsByLetter('A')">A</button>
                <button onclick="filtrerClientsByLetter('B')">B</button>
                <button onclick="filtrerClientsByLetter('C')">C</button>
                <button onclick="filtrerClientsByLetter('D')">D</button>
                <button onclick="filtrerClientsByLetter('E')">E</button>
                <button onclick="filtrerClientsByLetter('F')">F</button>
                <button onclick="filtrerClientsByLetter('G')">G</button>
                <button onclick="filtrerClientsByLetter('H')">H</button>
                <button onclick="filtrerClientsByLetter('I')">I</button>
                <button onclick="filtrerClientsByLetter('J')">J</button>
                <button onclick="filtrerClientsByLetter('K')">K</button>
                <button onclick="filtrerClientsByLetter('L')">L</button>
                <button onclick="filtrerClientsByLetter('M')">M</button>
                <button onclick="filtrerClientsByLetter('N')">N</button>
                <button onclick="filtrerClientsByLetter('O')">O</button>
                <button onclick="filtrerClientsByLetter('P')">P</button>
                <button onclick="filtrerClientsByLetter('Q')">Q</button>
                <button onclick="filtrerClientsByLetter('R')">R</button>
                <button onclick="filtrerClientsByLetter('S')">S</button>
                <button onclick="filtrerClientsByLetter('T')">T</button>
                <button onclick="filtrerClientsByLetter('U')">U</button>
                <button onclick="filtrerClientsByLetter('V')">V</button>
                <button onclick="filtrerClientsByLetter('W')">W</button>
                <button onclick="filtrerClientsByLetter('X')">X</button>
                <button onclick="filtrerClientsByLetter('Y')">Y</button>
                <button onclick="filtrerClientsByLetter('Z')">Z</button>
            </div>
        </div>
        
        <input type="text" id="search-clients" placeholder="Rechercher par nom, prénom ou téléphone" onkeyup="filtrerClients()">
        <table id="clients-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Modification</th>
                    <th>Suppression</th>
                </tr>
            </thead>
            <tbody id="clients">
                <?php
                // Connexion à la base de données
                $conn = new mysqli("localhost", "root", "", "amal");

                // Vérifier la connexion
                if ($conn->connect_error) {
                    die("La connexion a échoué : " . $conn->connect_error);
                }

                // Requête SQL pour récupérer tous les clients triés par nom
$sql = "SELECT id, nom, prenom, telephone, email FROM clients ORDER BY nom ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Affichage des données de chaque ligne
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["nom"]) . "</td>
                                <td>" . htmlspecialchars($row["prenom"]) . "</td>
                                <td>" . htmlspecialchars($row["telephone"]) . "</td>
                                <td><a href='mailto:" . htmlspecialchars($row["email"]) . "'>" . htmlspecialchars($row["email"]) . "</a></td>
                                <td><button onclick='afficherFormulaireModifications(" . $row["id"] . ")'><i class='fas fa-edit'></i> Modifier</button></td>
                                <td><button class='btn-supprimer' onclick='supprimerClient(" . $row["id"] . ")'><i class='fas fa-trash-alt'></i> Supprimer</button></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Aucun client trouvé.</td></tr>";
                }

                // Fermeture de la connexion
                $conn->close();
                ?>
                <!-- Ligne pour "Aucun client trouvé" -->
                <tr id="no-results-row" style="display: none;">
                    <td colspan="6" style="text-align: center; color: #cc0000; font-weight: bold;">Aucun client trouvé.</td>
                </tr>
            </tbody>
        </table>
        <div id="message" class="message"></div>
    </div>
    <script>
    let currentLetter = '';
    let currentSessionId = '';
    let currentSearch = '';

    // Fonction pour charger tous les clients initialement
    function loadAllClients() {
        fetchClients();
    }

    function filtrerClients() {
        currentSearch = document.getElementById('search-clients').value.trim().toLowerCase();
        currentSessionId = document.getElementById('session-select').value;

        fetchClients();
    }

    function filtrerClientsByLetter(letter) {
    currentLetter = letter;
    document.getElementById('search-clients').value = ''; // Réinitialiser le champ de recherche

    // Retirer la classe 'active' de tous les boutons
    let buttons = document.querySelectorAll('.alphabet-filter button');
    buttons.forEach(button => button.classList.remove('active'));

    // Ajouter la classe 'active' au bouton cliqué
    let selectedButton = Array.from(buttons).find(button => button.textContent === (letter || 'Tous'));
    if (selectedButton) {
        selectedButton.classList.add('active');
    }

    fetchClients();
}


    function fetchClients() {
        fetch('fetch_clients.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'session_id': currentSessionId,
                'letter': currentLetter
            })
        })
        .then(response => response.json())
        .then(data => {
            const filteredData = data.filter(client => {
                const nom = client.nom.toLowerCase();
                const prenom = client.prenom.toLowerCase();
                const telephone = client.telephone.toLowerCase();

                return (nom.includes(currentSearch) || prenom.includes(currentSearch) || telephone.includes(currentSearch)) &&
                       (currentLetter === '' || nom.startsWith(currentLetter.toLowerCase()));
            });

            const tbody = document.getElementById('clients');
            tbody.innerHTML = '';

            if (filteredData.length > 0) {
                filteredData.forEach(client => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${client.nom}</td>
                        <td>${client.prenom}</td>
                        <td>${client.telephone}</td>
                        <td><a href='mailto:${client.email}'>${client.email}</a></td>
                        <td><button onclick='afficherFormulaireModifications(${client.id})'><i class='fas fa-edit'></i> Modifier</button></td>
                        <td><button class='btn-supprimer' onclick='supprimerClient(${client.id})'><i class='fas fa-trash-alt'></i> Supprimer</button></td>
                    `;
                    tbody.appendChild(row);
                });
                document.getElementById('message').innerText = '';
            } else {
                tbody.innerHTML = "<tr><td colspan='6' style='text-align: center; color: #cc0000; font-weight: bold;'>Aucun client trouvé.</td></tr>";
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function retourAccueil() {
        window.location.href = 'index.php';
    }

    function afficherFormulaireModifications(id) {
        window.location.href = `modifier.php?id=${id}`;
    }

    function supprimerClient(id) {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce client ?")) {
            window.location.href = `supprimer_client.php?id=${id}`;
        }
    }

    // Charger tous les clients au début
    document.addEventListener('DOMContentLoaded', loadAllClients);
    </script>
</body>
</html>
