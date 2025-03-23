<?php
session_start();
require 'config.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les fiches de frais de l'utilisateur
$stmt = $pdo->prepare("SELECT id, mois, montantValide, idEtat FROM FichesFrais WHERE utilisateurId = ? ORDER BY mois DESC");
$stmt->execute([$user_id]);
$fiches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Fiches de Frais</title>
    <link rel="stylesheet" href="fiches.css"> 
</head>
<body>
<header>
    <img src="gsb.png" alt="Logo GSB">
    <nav>
        <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="FicheFrais.php">Renseigner Fiche Frais</a></li>
            <li><a href="monProfil.php">Mon Profil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>
<h1>Mes Fiches de Frais</h1>

<?php if (empty($fiches)): ?>
    <p>Aucune fiche de frais trouvée.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID Fiche</th>
                <th>Mois</th>
                <th>Montant Validé</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fiches as $fiche): ?>
                <tr>
                    <td><?= htmlspecialchars($fiche['id']) ?></td>
                    <td><?= htmlspecialchars(date('Ym', strtotime($fiche['mois'] . '01'))) ?></td>
                    <td><?= htmlspecialchars(number_format($fiche['montantValide'], 2)) ?> €</td>
                    <td><?= htmlspecialchars($fiche['idEtat']) ?></td>
                    <td>
                        <a href="consulterFiche.php?id=<?= $fiche['id'] ?>">Consulter</a> |
                        <a href="modifierFiche.php?id=<?= $fiche['id'] ?>">Modifier</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>