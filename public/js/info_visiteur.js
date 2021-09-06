"use strict";

//***************************************************************************************
// Création d'un formulaire permettant de récupérer les données js de résolution d'écran
//***************************************************************************************
let divResolution = document.getElementById('div_resolution');
let formResolution = document.createElement('form');
formResolution.setAttribute('id','form_resolution');
formResolution.setAttribute('name','form_resolution');
formResolution.setAttribute('method','POST');
divResolution.appendChild(formResolution);

let inputWidth = document.createElement("input");
inputWidth.setAttribute('name','width');
inputWidth.setAttribute('type','hidden');
inputWidth.setAttribute('value',screen.width);
formResolution.appendChild(inputWidth);

let inputHeight = document.createElement("input");
inputHeight.setAttribute('name','height');
inputHeight.setAttribute('type','hidden');
inputHeight.setAttribute('value',screen.height);
formResolution.appendChild(inputHeight);

function getResolution () {
    setTimeout(() => { document.forms["form_resolution"].submit(); }, 1000);
}


(function() {
    if (post == 1) {
        return;
    }
    return getResolution();
} ())