<?php
session_start();
date_default_timezone_set('Africa/Casablanca');

const FICHIER = __DIR__ . '/data/etudiants.txt';
const FILIERES = ['GI', 'IDIA', 'GE', 'GC'];
const UTILISATEURS = [
    'admin' => 'admin123',
    'prof'  => 'php2026',
];

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function connecte(): bool
{
    return isset($_SESSION['login']);
}

function exiger_connexion(): void
{
    if (!connecte()) {
        header('Location: index.php');
        exit;
    }
}

function lire_etudiants(): array
{
    $liste = [];
    if (!file_exists(FICHIER)) {
        return $liste;
    }
    $lignes = file(FICHIER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lignes as $ligne) {
        $c = explode(';', $ligne);
        if (count($c) < 6) {
            continue; // ligne mal formée, on l'ignore
        }
        $id = (int) $c[0];
        $liste[$id] = [
            'id'      => $id,
            'nom'     => $c[1],
            'prenom'  => $c[2],
            'filiere' => $c[3],
            'note'    => (float) $c[4],
            'photo'   => $c[5],
        ];
    }
    return $liste;
}

function sauver_etudiants(array $liste): void
{
    $contenu = '';
    foreach ($liste as $e) {
        $contenu .= implode(';', [$e['id'], $e['nom'], $e['prenom'], $e['filiere'], $e['note'], $e['photo']]) . "\n";
    }
    file_put_contents(FICHIER, $contenu, LOCK_EX);
}

function nettoyer(string $s): string
{
    $s = str_replace([';', "\r", "\n"], ' ', $s);
    return trim($s);
}