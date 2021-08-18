"use strict";

document.getElementById("after").style.display = "none";

let intervalId = null;

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



function finish() {
    clearInterval(intervalId);
    document.getElementById("bip").innerHTML = 'Envoi du groupe de destinataires n° '+ (index+1)+ ' va partir';
    // alert('Envoi n° '+ (index+1)+ ' prêt à partir');
    setTimeout(() => { document.forms["form_index"].submit(); }, 2000);
    
    

}

function bip() {
    counter--;
    if (counter == 0) finish();
    else {
        document.getElementById("bip").innerHTML = counter + " secondes avant prochain envoi";
    }
}

(function() {
    if (index>index_max) {
        document.getElementById("form").style.display = "none";
        document.getElementById("after").style.display = "block";
        return;
    }
    intervalId = setInterval(bip, 1000);
} ())
