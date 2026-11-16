<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= h($titre ?? 'Gestion des étudiants') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Gestion des étudiants</h1>
    <?php if (connecte()): ?>
        <nav>
            <a href="liste.php">Liste</a>
            <a href="ajouter.php">Ajouter</a>
            <a href="logout.php">Déconnexion (<?= h($_SESSION['login']) ?>)</a>
        </nav>
    <?php endif; ?>
</header>
<main>