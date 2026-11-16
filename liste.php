<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

// recherche
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $etudiants = array_filter($etudiants, function ($e) use ($q) {
        return stripos($e['nom'], $q) !== false || stripos($e['prenom'], $q) !== false;
    });
}

// tri
$tris = [
    'ajout'   => "Ordre d'ajout",
    'nom'     => 'Nom',
    'filiere' => 'Filière',
    'note'    => 'Note (décroissante)',
];
$tri = $_GET['tri'] ?? 'ajout';
if (!in_array($tri, array_keys($tris))) {
    $tri = 'ajout';
}

usort($etudiants, function ($a, $b) use ($tri) {
    if ($tri === 'nom')     return strcasecmp($a['nom'], $b['nom']);
    if ($tri === 'filiere') return strcasecmp($a['filiere'], $b['filiere']);
    if ($tri === 'note')    return $b['note'] <=> $a['note'];
    return $a['id'] <=> $b['id'];
});

// statistiques
$nb        = count($etudiants);
$notes     = array_column($etudiants, 'note');
$moyenne   = $nb > 0 ? array_sum($notes) / $nb : 0;
$meilleure = $nb > 0 ? max($notes) : 0;

// msg flash 
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$titre = 'Liste des étudiants';
include 'header.php';
?>

<h2>Liste des étudiants</h2>

<?php if ($flash !== ''): ?>
    <p class="succes"><?= h($flash) ?></p>
<?php endif; ?>

<!--formulaire recherche et tri -->
<form method="get" class="recherche">
    <input type="text" name="q" id="recherche" value="<?= h($q) ?>" placeholder="Nom ou prénom">
    <select name="tri">
        <?php foreach ($tris as $valeur => $libelle): ?>
            <option value="<?= $valeur ?>" <?= $tri === $valeur ? 'selected' : '' ?>><?= h($libelle) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Rechercher</button>
    <a href="liste.php">Réinitialiser</a>
</form>

<!--statistiques-->
<p class="stats">
    <?= $nb ?> étudiant(s) affiché(s) –
    Moyenne : <?= number_format($moyenne, 2) ?> –
    Meilleure note : <?= $nb > 0 ? $meilleure : '—' ?>
</p>

<table>
    <thead>
        <tr>
            <th>Photo</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Filière</th>
            <th>Note</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($etudiants) === 0): ?>
        <tr><td colspan="6">Aucun étudiant</td></tr>
    <?php else: ?>
        <?php foreach ($etudiants as $e): ?>
            <tr>
                <td>
                    <?php if ($e['photo'] !== ''): ?>
                        <img src="uploads/<?= h($e['photo']) ?>" alt="photo" class="photo">
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td><?= h(strtoupper($e['nom'])) ?></td>
                <td><?= h(ucfirst($e['prenom'])) ?></td>
                <td><?= h($e['filiere']) ?></td>
                <td class="<?= $e['note'] >= 10 ? 'note-ok' : 'note-ko' ?>"><?= $e['note'] ?></td>
                <td>
                    <a href="ajouter.php?id=<?= $e['id'] ?>">Modifier</a>
                    <a href="supprimer.php?id=<?= $e['id'] ?>" class="supprimer">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>