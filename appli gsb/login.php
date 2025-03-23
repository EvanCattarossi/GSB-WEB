<?php
session_start();
require 'config.php';

// Récupération des données soumises

$email = $_POST['email'];
$password = $_POST['password'];

try {
    // Préparation de la requête pour récupérer l'utilisateur avec l'email
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    // Récupération des résultats
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe
    if ($utilisateur && password_verify($password, $utilisateur['password'])) {
        // Connexion réussie : définir les variables de session
        $_SESSION['role'] = $utilisateur['role'];
        $_SESSION['email'] = $email;  
        $_SESSION['user_id'] = $utilisateur["id"];
        
        // Rediriger l'utilisateur vers le dashboard approprié en fonction de son rôle
        if ($utilisateur['role'] == 'administrateur') {
            header('Location: dashboard_admin.php');  // Rediriger vers le dashboard administrateur 
        } elseif ($utilisateur['role'] == 'comptable') {
            header('Location: dashboard_comptable.php');  // Rediriger vers le dashboard comptable
        } else {
            // Si le rôle est "visiteur", rediriger vers le profil du visiteur
            header('Location: dashboard_visiteur.php');  // Rediriger vers la page du profil du visiteur
        }
        exit();
    } else {
        // Connexion échouée
        echo "<p>Nom d'utilisateur ou mot de passe incorrect.</p>";
        echo "<a href='login.php'>Réessayer</a>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
