// ===== 1. Confirmation avant suppression =====
document.querySelectorAll('a.supprimer').forEach(function (lien) {
    lien.addEventListener('click', function (event) {
        if (!confirm('Voulez-vous vraiment supprimer cet étudiant ?')) {
            event.preventDefault(); // annule le clic : on ne suit pas le lien
        }
    });
});

// ===== 2. Recherche instantanée =====
const champ = document.querySelector('#recherche');

if (champ) { // le champ n'existe que sur liste.php
    champ.addEventListener('input', function () {
        const texte = champ.value.trim().toLowerCase();

        document.querySelectorAll('table tbody tr').forEach(function (ligne) {
            const cellules = ligne.querySelectorAll('td');
            if (cellules.length < 3) {
                return; // ligne "Aucun étudiant" : on n'y touche pas
            }
            const nomPrenom = (cellules[1].textContent + ' ' + cellules[2].textContent).toLowerCase();
            ligne.style.display = nomPrenom.includes(texte) ? '' : 'none';
        });
    });
}