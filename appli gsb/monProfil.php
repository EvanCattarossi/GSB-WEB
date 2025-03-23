<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
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

// Gérer la soumission du formulaire pour mettre à jour les informations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    // Mettre à jour les informations dans la base de données
    $updateQuery = $pdo->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email WHERE id = :id");
    $updateQuery->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'id' => $userId
    ]);

    echo "Vos informations ont été mises à jour avec succès.";
    // Rafraîchir les données affichées
    $user['nom'] = $nom;
    $user['prenom'] = $prenom;
    $user['email'] = $email;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="profil.css" rel="stylesheet">
    <title>Mon Profil</title>
</head>
<body>
<header>
        <img src="gsb.png" alt="Logo GSB"> <nav>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="monProfil.php">Mon Profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <h1>Mon Profil</h1>
    <form method="post" action="">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required><br>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>
