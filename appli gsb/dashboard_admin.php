<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté et administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: login_visiteur.php');
    exit();
}

// Récupérer les données de l'utilisateur connecté
$userId = $_SESSION['user_id'];
$query = $pdo->prepare("SELECT nom, prenom, email FROM utilisateurs WHERE id = :id");
$query->execute(['id' => $userId]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

// Ajouter un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hachage du mot de passe
    $role = $_POST['role'];

    // Insérer le nouvel utilisateur dans la base de données
    $addUserQuery = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, password, role) VALUES (:nom, :prenom, :email, :password, :role)");
    $addUserQuery->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'password' => $password,
        'role' => $role
    ]);

    echo "Utilisateur ajouté avec succès.";
}

// Supprimer un utilisateur
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Ne pas permettre à l'admin de se supprimer lui-même
    if ($deleteId != $userId) {
        $deleteQuery = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $deleteQuery->execute(['id' => $deleteId]);
        echo "Utilisateur supprimé avec succès.";
    } else {
        echo "Vous ne pouvez pas vous supprimer.";
    }
}

// Modifier un utilisateur
if (isset($_POST['edit_user']) && isset($_POST['user_id'])) {
    $userIdToEdit = $_POST['user_id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Mettre à jour les informations de l'utilisateur
    $updateQuery = $pdo->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, role = :role WHERE id = :id");
    $updateQuery->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'role' => $role,
        'id' => $userIdToEdit
    ]);

    echo "Utilisateur mis à jour avec succès.";
}

// Récupérer tous les utilisateurs
$query = $pdo->query("SELECT id, nom, prenom, email, role FROM utilisateurs");
$utilisateurs = $query->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre d'utilisateurs par rôle pour le graphique
$rolesCount = [];
$rolesQuery = $pdo->query("SELECT role, COUNT(*) AS count FROM utilisateurs GROUP BY role");
while ($row = $rolesQuery->fetch(PDO::FETCH_ASSOC)) {
    $rolesCount[$row['role']] = $row['count'];
}

// Récupérer les moyennes de remboursement pour les frais forfaitisés
$fraisMoyens = [];
$fraisQuery = $pdo->query("SELECT typeForfait, AVG(montant) AS moyenne FROM ElementsForfaitises GROUP BY typeForfait");
while ($row = $fraisQuery->fetch(PDO::FETCH_ASSOC)) {
    $fraisMoyens[$row['typeForfait']] = $row['moyenne'];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="profil.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <img src="gsb.png" alt="Logo GSB">
        <nav>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="monProfil.php">Mon Profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <h1>Gestion des Utilisateurs</h1>

    <h2>Ajouter un Utilisateur</h2>
    <form method="POST" action="">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br>

        <label for="role">Rôle :</label>
        <select id="role" name="role" required>
            <option value="visiteur">Visiteur</option>
            <option value="comptable">Comptable</option>
            <option value="administrateur">Administrateur</option>
        </select><br>

        <button type="submit" name="add_user">Ajouter</button>
    </form>

    <h2>Liste des Utilisateurs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $utilisateur): ?>
            <tr>
                <td><?= $utilisateur['id'] ?></td>
                <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
                <td><?= htmlspecialchars($utilisateur['prenom']) ?></td>
                <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                <td><?= htmlspecialchars($utilisateur['role']) ?></td>
                <td>
                    <a href="?delete_id=<?= $utilisateur['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                    <a href="?edit_id=<?= $utilisateur['id'] ?>">Modifier</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Statistiques des Remboursements</h2>
    <div style="width: 600px; height: 400px;">
    <canvas id="fraisMoyensPieChart"></canvas>²
    <canvas id="fraisMoyensBarChart" ></canvas>
</div>

<script>
    // ... (Votre code JavaScript existant pour le graphique) ...
</script>

    <script>
    var fraisMoyens = <?php echo json_encode($fraisMoyens); ?>; // Récupérer les données PHP

    // Graphique en camembert
    var ctxPie = document.getElementById('fraisMoyensPieChart').getContext('2d');
    var fraisMoyensPieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: Object.keys(fraisMoyens),
            datasets: [{
                label: 'Moyenne de Remboursement',
                data: Object.values(fraisMoyens),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Moyenne de Remboursement par Frais (Camembert)'
            }
        }
    }); // Parenthèse fermante ajoutée ici

    // Graphique en barres
    var ctxBar = document.getElementById('fraisMoyensBarChart').getContext('2d');
    var fraisMoyensBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: Object.keys(fraisMoyens),
            datasets: [{
                label: 'Moyenne de Remboursement',
                data: Object.values(fraisMoyens),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            title: {
                display: true,
                text: 'Moyenne de Remboursement par Frais (Barres)'
            }
        }
    }); // Parenthèse fermante ajoutée ici
</script>