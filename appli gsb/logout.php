<?php
session_start(); // Démarre ou reprend la session en cours

// Détruit toutes les données de la session
session_unset(); 

// Détruit la session
session_destroy();

// Redirige vers la page de connexion 
header('Location: index.html');
exit;
?>