<?php
require_once 'functions.php';
exiger_connexion();

$id = (int) ($_GET['id'] ?? 0);
$etudiants = lire_etudiants();

if (isset($etudiants[$id])) {
    // Supprimer la photo si elle existe
    $photo = $etudiants[$id]['photo'];
    if ($photo !== '' && file_exists(__DIR__ . '/uploads/' . $photo)) {
        unlink(__DIR__ . '/uploads/' . $photo);
    }

    // Retirer l'étudiant du tableau puis réécrire le fichier
    unset($etudiants[$id]);
    sauver_etudiants($etudiants);

    $_SESSION['flash'] = 'Étudiant supprimé.';
}

header('Location: liste.php');
exit;