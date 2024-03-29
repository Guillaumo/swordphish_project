"use strict";

// on cache le div qui affiche le nombre de mails envoyés et les actions à effectuer par la suite
document.getElementById("after").style.display = "none";

let intervalId = null;

// création et affichage d'éléments du DOM pour montrer la progession d'envois des groupes d'emails
let div = document.getElementById("first");

let p1 = document.createElement("p");
p1.textContent = 'Envoi encours du groupe de destinataires n° ' + index;
div.appendChild(p1);

let p2 = document.createElement("p");
p2.setAttribute('id','bip');
p2.setAttribute('class','alert');
p2.setAttribute('class','alert-danger');
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

let img = document.createElement('img');
// let gifPath = img.dataset.gifPath;
img.setAttribute("src",'/images/loading.gif');
img.setAttribute("class","text-center");
// img.src = "{{ asset('images/loading.gif') }}";
div.appendChild(img);

// fonctions pour la temporisation, aller chercher les groupes de mails successifs
// quand la temporisation est terminée, envoi au server l'index suivant pour l'envoi des mails du groupe suivant
function finish() {
    clearInterval(intervalId);
    document.getElementById("bip").innerHTML = 'Attention ! Ne pas fermer votre navigateur tant que l\'envoi d\'emails n\'est pas terminé <br><br> Envoi du groupe de destinataires n° '+ (index+1)+ ' va partir';
    setTimeout(() => { document.forms["form_index"].submit(); }, 2000);
}

// compteur
function bip() {
    counter--;
    if (counter == 0) finish();
    else {
        document.getElementById("bip").innerHTML = 'Attention ! Ne pas fermer votre navigateur tant que l\'envoi d\'emails n\'est pas terminé <br><br> ' + counter + " secondes avant prochain envoi";
    }
}

// fonction immédiate pour le lancement du script
(function() {
    if (index>index_max) {
        setTimeout(() => { 
            document.getElementById("first").style.display = "none";
            document.getElementById("after").style.display = "block";
        }, 3000);
        
        return;
    }
    intervalId = setInterval(bip, 1000);
} ())
