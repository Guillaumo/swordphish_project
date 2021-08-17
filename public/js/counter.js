// let counter = data;
let intervalId = null;

// window.addEventListener("load", function(event) {
//     start();
// });

function sendDataToPhp() {
    // Création de l'objet XMLHttpRequest
    let xhr = getXMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // let response = JSON.parse(xhr.responseText);
                let response = xhr.responseText;
                alert('Envoi n° '+ response.index + 1 + ' parti');
                index = response.index + 1;
                counter = response.counter;
                if(index <= index_max)
                {
                    return start();
                }
            } else {
                alert('Un problème est survenu avec la requête.');
            }
        }
        console.log("xhr2: ", xhr, 'état : ',xhr.readyState);
    };

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.send('index =' + encodeURIComponent(index));

    console.log("xhr1: ", xhr, 'état : ',xhr.readyState);
}

function finish() {
    clearInterval(intervalId);
    document.getElementById("bip").innerHTML = "Nouvel envoi";

    return sendDataToPhp();
}

function bip() {
    counter--;
    if (counter == 0) finish();
    else {
        document.getElementById("bip").innerHTML = counter + " secondes restantes";
    }
}


(function() {
    intervalId = setInterval(bip, 1000);
} ())

function getXMLHttpRequest() {
    let xhr = null;

    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
        } else {
            xhr = new XMLHttpRequest();
        }
    } else {
        alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
        return null;
    }
    return xhr;
}