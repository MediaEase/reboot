export function copyToClipboard(elementId) {
    var text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(function () {
        console.log('Texte copié avec succès!');
    })
        .catch(function (error) {
            console.error('Erreur lors de la copie:', error);
        });
}

window.copyToClipboard = copyToClipboard;
