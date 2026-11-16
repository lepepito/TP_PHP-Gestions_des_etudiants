<?php
require_once 'functions.php';

$etudiants = [
    1 => ['id' => 1, 'nom' => 'benali',  'prenom' => 'sara',    'filiere' => 'GI',   'note' => 14.5, 'photo' => ''],
    2 => ['id' => 2, 'nom' => 'alaoui',  'prenom' => 'youssef', 'filiere' => 'GE',   'note' => 8.75, 'photo' => ''],
    3 => ['id' => 3, 'nom' => 'idrissi', 'prenom' => 'nora',    'filiere' => 'IDIA', 'note' => 17,   'photo' => ''],
];

sauver_etudiants($etudiants);

echo '<h2>Contenu brut du fichier</h2>';
echo '<pre>' . h(file_get_contents(FICHIER)) . '</pre>';

echo '<h2>Relu avec lire_etudiants()</h2>';
echo '<pre>';
print_r(lire_etudiants());
echo '</pre>';

echo '<h2>Test nettoyer()</h2>';
echo '[' . h(nettoyer("  Ben;ali \n ")) . ']';