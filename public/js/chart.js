"use strict";
// graphique du nombre de liens cliqués sur le nombre d'emails envoyés
let clicks = document.getElementById('clicks_stat').getContext('2d');
let clicks_graph = new Chart (clicks, {
    type: "doughnut",
    data: {
        labels: ["Nombre d'emails envoyés", "Nombre de liens cliqués"],
        datasets: [{
            label: "Nombre de liens cliqués sur x emails envoyés",
            data: data_clicks,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
              ],
            hoverOffset: 4,
        }]
    },
    options: {
        title: {
            display: true,
            text: 'Nombre de liens cliqués sur le nombre d\'emails envoyés',
            fontSize: 24,
            fontColor: '#292d42',
        },
        legend: {
            labels: {
                fontSize: 18,
            },
        },
        plugins: {
            labels: {
                render: 'value',
                fontSize: 18,
                fontColor: '#292d42',
                fontStyle: 'bold',
            }
        },
        animation: {
            onComplete: function() {
                console.log('je suis dans la fonctononAnimationComplete');
                clicks.font = "48px";
                clicks.fillStyle = "#292d42";
                clicks.textAlign = "center";
                clicks.fillText((data_clicks[1]*100/data_clicks[0]).toFixed(0) + "%", 620, 270);
            }
        },

    }
});
//  graphique du nombre de formulaire soumis sur le nombre de liens cliqués
let submits = document.getElementById('submits_stat').getContext('2d');
let submits_graph = new Chart (submits, {
    type: "doughnut",
    data: {
        labels: ["Nombre de liens cliqués", "Nombre de formulaires soumis"],
        datasets: [{
            label: "Nombre de formulaires soumis sur x liens cliqués",
            data: data_submits,
            backgroundColor: [
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
              ],
        }]
    },
    options: {
        title: {
            display: true,
            text: 'Nombre de formulaires soumis sur le nombre de liens cliqués',
            fontSize: 24,
            fontColor: '#292d42',
        },
        legend: {
            labels: {
                fontSize: 18,
                fontColor: '#292d42',
            }
        },
        plugins: {
            labels: {
                render: 'value',
                fontSize: 18,
                fontColor: '#292d42',
                fontStyle: 'bold',
            }
        },
        animation: {
            onComplete: function() {
                console.log('je suis dans la fonctononAnimationComplete');
                submits.font = "48px";
                submits.fillStyle = "#292d42";
                submits.textAlign = "center";
                submits.fillText((data_submits[1]*100/data_submits[0]).toFixed(0) + "%", 620, 270);
            }
        },
    }
});