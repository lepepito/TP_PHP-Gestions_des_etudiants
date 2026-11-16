<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

// ===== NOUVEAU : mode modification si l'URL contient ?id= =====
$id    = (int) ($_GET['id'] ?? 0);
$modif = $id > 0;

if ($modif && !isset($etudiants[$id])) {
    header('Location: liste.php');   // id inexistant : retour à la liste
    exit;
}

$erreurs = [];
$nom     = '';
$prenom  = '';
$filiere = '';
$note    = '';

// ===== NOUVEAU : pré-remplir avec les données de l'étudiant =====
if ($modif) {
    $nom     = $etudiants[$id]['nom'];
    $prenom  = $etudiants[$id]['prenom'];
    $filiere = $etudiants[$id]['filiere'];
    $note    = (string) $etudiants[$id]['note'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupérer et nettoyer les valeurs
    $nom     = nettoyer($_POST['nom'] ?? '');
    $prenom  = nettoyer($_POST['prenom'] ?? '');
    $filiere = $_POST['filiere'] ?? '';
    $note    = str_replace(',', '.', trim($_POST['note'] ?? ''));

    // 2. Valider
    if ($nom === '') {
        $erreurs[] = 'Le nom est obligatoire.';
    }
    if ($prenom === '') {
        $erreurs[] = 'Le prénom est obligatoire.';
    }
    if (!in_array($filiere, FILIERES, true)) {
        $erreurs[] = 'Veuillez choisir une filière valide.';
    }
    if (!is_numeric($note) || $note < 0 || $note > 20) {
        $erreurs[] = 'La note doit être un nombre entre 0 et 20.';
    }

    // Valider la photo (facultative)
    $fichier = $_FILES['photo'] ?? null;
    $avecPhoto = $fichier !== null && $fichier['error'] !== UPLOAD_ERR_NO_FILE;
    if ($avecPhoto) {
        $ext = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = "Erreur lors de l'envoi de la photo.";
        } elseif (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            $erreurs[] = 'La photo doit être au format jpg, jpeg ou png.';
        } elseif ($fichier['size'] > 1024 * 1024) {
            $erreurs[] = 'La photo ne doit pas dépasser 1 Mo.';
        }
    }

    // 3. Si aucune erreur : enregistrer et rediriger
    if (count($erreurs) === 0) {
        // ===== NOUVEAU : en modification, on garde l'ancienne photo par défaut =====
        $photo = $modif ? $etudiants[$id]['photo'] : '';

        if ($avecPhoto) {
            // ===== NOUVEAU : supprimer l'ancienne photo si on en envoie une nouvelle =====
            if ($photo !== '' && file_exists(__DIR__ . '/uploads/' . $photo)) {
                unlink(__DIR__ . '/uploads/' . $photo);
            }
            $photo = 'etu_' . uniqid() . '.' . $ext;
            move_uploaded_file($fichier['tmp_name'], __DIR__ . '/uploads/' . $photo);
        }

        // ===== NOUVEAU : nouvel id seulement en mode ajout =====
        if (!$modif) {
            $id = count($etudiants) > 0 ? max(array_keys($etudiants)) + 1 : 1;
        }

        $etudiants[$id] = [
            'id'      => $id,
            'nom'     => strtolower($nom),
            'prenom'  => strtolower($prenom),
            'filiere' => $filiere,
            'note'    => (float) $note,
            'photo'   => $photo,
        ];
        sauver_etudiants($etudiants);

        $_SESSION['flash'] = $modif ? 'Étudiant modifié avec succès.' : 'Étudiant ajouté avec succès.';
        header('Location: liste.php');
        exit;
    }
}

$titre = $modif ? 'Modifier un étudiant' : 'Ajouter un étudiant';
include 'header.php';
?>

<h2><?= $titre ?></h2>

<?php if (count($erreurs) > 0): ?>
    <div class="erreur">
        <ul>
            <?php foreach ($erreurs as $err): ?>
                <li><?= h($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="carte">
    <label>Nom
        <input type="text" name="nom" value="<?= h($nom) ?>">
    </label>
    <label>Prénom
        <input type="text" name="prenom" value="<?= h($prenom) ?>">
    </label>
    <label>Filière
        <select name="filiere">
            <option value="">-- Choisir --</option>
            <?php foreach (FILIERES as $f): ?>
                <option value="<?= $f ?>" <?= $filiere === $f ? 'selected' : '' ?>><?= $f ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Note
        <input type="text" name="note" value="<?= h($note) ?>">
    </label>

    <!-- NOUVEAU : afficher la photo actuelle en modification -->
    <?php if ($modif && $etudiants[$id]['photo'] !== ''): ?>
        <p>Photo actuelle : <img src="uploads/<?= h($etudiants[$id]['photo']) ?>" alt="photo" class="photo"></p>
    <?php endif; ?>

    <label>Photo (facultative – jpg, jpeg, png – 1 Mo max)
        <input type="file" name="photo" accept=".jpg,.jpeg,.png">
    </label>
    <button type="submit">Enregistrer</button>
    <a href="liste.php">Annuler</a>
</form>

<?php include 'footer.php'; ?>