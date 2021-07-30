"use strict";
// /**
//  * Fonction pour instancier un nouvel objet XMLHttpRequest
//  * @returns 
//  */
// function getXMLHttpRequest() {
// 	let xhr = null;
	
// 	if (window.XMLHttpRequest || window.ActiveXObject) {
// 		if (window.ActiveXObject) {
// 			try {
// 				xhr = new ActiveXObject("Msxml2.XMLHTTP");
// 			} catch(e) {
// 				xhr = new ActiveXObject("Microsoft.XMLHTTP");
// 			}
// 		} else {
// 			xhr = new XMLHttpRequest(); 
// 		}
// 	} else {
// 		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
// 		return null;
// 	}
	
// 	return xhr;
// }

// // Création de l'objet XMLHttpRequest
// let xhr = getXMLHttpRequest();

// // Données récupérées par le JS
// let width_screen = screen.width;
// let height_screen = screen.height;

// // Envoi des données au serveur
// xhr.open("POST", "DestinataireController.php", true);
// xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
// // Alternative : xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
// xhr.send("variable1=width_screen&variable2=height_screen");

//******************************************************************************
// Création d' un element div ( Merci à Coucou747 )
//******************************************************************************
// const box = document.createElement('div');
// document.body.appendChild(box); // On envoie le tout
const box = document.getElementById('js');

//******************************************************************************
// Largeur X Hauteur
//******************************************************************************
box.innerHTML = 'Votre résolution est de ' + screen.width + ' X ' + screen.height + '.';

//******************************************************************************
// Plug-ins
//******************************************************************************
// Nombre de plug-ins installés
let nbplugin = navigator.plugins.length;
if (nbplugin) {
    box.innerHTML += 'Plug-ins installés ( ' + nbplugin + ' ) :';

    let i = -1;
    while (i < nbplugin) { // Affichage des noms des plug-ins
        i++;
        box.innerHTML += ' ' + navigator.plugins[i].name + ', ';
    }
} else {
    box.innerHTML += 'Il n\' y a aucun plug-ins installé.';
}