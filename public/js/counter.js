"use strict";

// on cache le div qui affiche le nombre de mails envoyés et les actions à effectuer par la suite
document.getElementById("after").style.display = "none";

let intervalId = null;

// création et affichage d'éléments du DOM pour montrer la progession d'envois des groupes d'emails
let div = document.getElementById("form");

let p1 = document.createElement("p");
p1.textContent = 'Envoi encours du groupe de destinataires n° ' + index;
div.appendChild(p1);

let p2 = document.createElement("p");
p2.setAttribute('id','bip');
p2.setAttribute('class','bg-danger');
div.appendChild(p2);

let form = document.createElement("form");
form.setAttribute('id','form_index');
form.setAttribute('name','form_index');
form.setAttribute('method','POST');
div.appendChild(form);

let input = document.createElement("input");
input.setAttribute('name','index');
input.setAttribute('type','hidden');
input.setAttribute('value',index);
form.appendChild(input);

// fonctions pour la temporisation, aller chercher les groupes de mails successifs
// quand la temporisation est terminée, envoi au server de l'index suivant pour l'envoi des mails du groupe suivant
function finish() {
    clearInterval(intervalId);
    document.getElementById("bip").innerHTML = 'Envoi du groupe de destinataires n° '+ (index+1)+ ' va partir';
    // alert('Envoi n° '+ (index+1)+ ' prêt à partir');
    setTimeout(() => { document.forms["form_index"].submit(); }, 2000);
}

// compteur
function bip() {
    counter--;
    if (counter == 0) finish();
    else {
        document.getElementById("bip").innerHTML = counter + " secondes avant prochain envoi";
    }
}

// fonction immédiate pour le lancement du script
(function() {
    if (index>index_max) {
        setTimeout(() => { 
            document.getElementById("form").style.display = "none";
            document.getElementById("after").style.display = "block";
        }, 3000);
        
        return;
    }
    intervalId = setInterval(bip, 1000);
} ())
