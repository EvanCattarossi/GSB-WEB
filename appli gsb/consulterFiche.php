<?php
session_start();
require 'config.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$fiche_id = $_GET['id'];

// Récupérer la fiche de frais
$stmt = $pdo->prepare("SELECT * FROM FichesFrais WHERE id = ? AND utilisateurId = ?");
$stmt->execute([$fiche_id, $user_id]);
$fiche = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fiche) {
    die("Fiche de frais non trouvée.");
}

// Récupérer les frais forfaitisés
$stmt = $pdo->prepare("SELECT * FROM ElementsForfaitises WHERE ficheFraisId = ?");
$stmt->execute([$fiche_id]);
$forfaits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les frais hors forfait
$stmt = $pdo->prepare("SELECT * FROM ElementsHorsForfait WHERE ficheFraisId = ?");
$stmt->execute([$fiche_id]);
$horsForfaits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation Fiche de Frais</title>
    <link rel="stylesheet" href="consulter.css"> 
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
<h1>Consultation Fiche de Frais</h1>

<h2>Fiche de Frais du <?= htmlspecialchars(date('Y-m', strtotime($fiche['mois'] . '01'))) ?></h2>

<h3>Frais Forfaitisés</h3>
<?php if (empty($forfaits)): ?>
    <p>Aucun frais forfaitisé trouvé.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Type de Frais</th>
                <th>Quantité</th>
                <th>Montant Unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($forfaits as $forfait): ?>
                <tr>
                    <td><?= htmlspecialchars($forfait['typeForfait']) ?></td>
                    <td><?= htmlspecialchars($forfait['quantite']) ?></td>
                    <td><?= htmlspecialchars(number_format($forfait['montant'], 2)) ?> €</td>
                    <td><?= htmlspecialchars(number_format($forfait['quantite'] * $forfait['montant'], 2)) ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h3>Frais Hors Forfait</h3>
<?php if (empty($horsForfaits)): ?>
    <p>Aucun frais hors forfait trouvé.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Libellé</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($horsForfaits as $horsForfait): ?>
                <tr>
                    <td><?= htmlspecialchars($horsForfait['date']) ?></td>
                    <td><?= htmlspecialchars($horsForfait['libelle']) ?></td>
                    <td><?= htmlspecialchars(number_format($horsForfait['montant'], 2)) ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><strong>Montant Validé :</strong> <?= htmlspecialchars(number_format($fiche['montantValide'], 2)) ?> €</p>

</body>
</html>