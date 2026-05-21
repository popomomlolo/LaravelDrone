/*
function initChart(apprentis, objectifFiltre = null) {

    if (!apprentis || apprentis.length === 0 || !document.getElementById('chartContainer')) {
        return;
    }

    const objectifsMap = {};

    apprentis.forEach(function (apprenti) {
        const reussisParObjectif = {};

        apprenti.sessions.forEach(function (session) {
            session.objectifs.forEach(function (obj) {
                // Si un objectif est filtré, ne compter que celui-ci
                if (objectifFiltre && obj.libelle !== objectifFiltre) {
                    return;
                }

                if (!reussisParObjectif[obj.libelle]) {
                    reussisParObjectif[obj.libelle] = false;
                }
                if (obj.reussi) {
                    reussisParObjectif[obj.libelle] = true;
                }
            });
        });

        Object.keys(reussisParObjectif).forEach(function (libelle) {
            if (!objectifsMap[libelle]) {
                objectifsMap[libelle] = { reussi: 0, echoue: 0 };
            }
            if (reussisParObjectif[libelle]) {
                objectifsMap[libelle].reussi++;
            } else {
                objectifsMap[libelle].echoue++;
            }
        });
    });

    const categories = Object.keys(objectifsMap);
    const dataReussi = categories.map(function (k) { return objectifsMap[k].reussi; });
    const dataEchoue = categories.map(function (k) { return objectifsMap[k].echoue; });
    const total = apprentis.length;

    console.log('Objectifs:', categories);
    console.log('Réussis:', dataReussi);
    console.log('Échoués:', dataEchoue);

    // Titre dynamique selon le filtre
    let titre = objectifFiltre
        ? 'Réussite : ' + objectifFiltre
        : 'Réussite par objectif';

    let sousTitre = objectifFiltre
        ? "Nombre d'apprentis ayant réussi ou échoué l'objectif (" + total + " apprentis)"
        : "Nombre d'apprentis ayant réussi ou échoué chaque objectif (" + total + " apprentis)";

    Highcharts.chart('chartContainer', {
        chart: {
            type: 'column',
            inverted: true,
            polar: true,
            backgroundColor: '#ffffff',
            style: { fontFamily: 'Raleway, sans-serif' }
        },
        title: {
            text: titre,
            style: { fontWeight: '600', fontSize: '1rem', color: '#000' }
        },
        subtitle: {
            text: sousTitre,
            style: { color: '#636b6f', fontSize: '15px' }
        },
        tooltip: {
            outside: true,
            formatter: function () {
                return '<b>' + this.series.name + '</b><br>' + //Mot reussi ou echoué
                    '<span style="color:' + this.color + '">●</span> ' +
                    '<b>' + this.y + '</b> / ' + total + ' apprentis';
            }
        },
        pane: {
            size: '100%',
            innerSize: '20%',
            endAngle: 270
        },
        xAxis: { //Titre entre chaque barre
            tickInterval: 1,
            labels: { align: 'right', allowOverlap: true, step: 1, y: 3, style: { fontSize: '16px', color: '#333', fontWeight: 'bold' } },
            lineWidth: 0,
            gridLineWidth: 0,
            categories: categories
        },
        yAxis: {
            lineWidth: 0,
            tickInterval: 1,
            reversedStacks: false,
            endOnTick: true,
            showLastLabel: true,
            gridLineWidth: 0
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                borderWidth: 0,
                pointPadding: 0,
                groupPadding: 0.15,
                borderRadius: { radius: '50%', where: 'all' }
            }
        },
        series: [
            { name: 'Réussi', data: dataReussi, color: '#22c55e' },
            { name: 'Échoué', data: dataEchoue, color: '#ef4444' }
        ],
        legend: { enabled: true, align: 'center', verticalAlign: 'bottom' },
        credits: { enabled: false }
    });
}*/




/**
 * Statistiquechart.js
 */
/**
 * Statistiquechart.js
 * Bar chart Highcharts — Réussite par objectif (colonnes droites, stacking %).
 * - Fond totalement transparent (hérite du site)
 * - Échoué (rouge) EN HAUT
 * - Réussi (vert) AU MILIEU
 * - Non tenté (gris) EN BAS
 */
function initChart(apprentis, objectifFiltre = null) {

    if (!apprentis || apprentis.length === 0 || !document.getElementById('chartContainer')) {
        return;
    }

    const objectifsMap = {};

    apprentis.forEach(function (apprenti) {
        const reussisParObjectif = {};

        apprenti.sessions.forEach(function (session) {
            session.objectifs.forEach(function (obj) {
                if (objectifFiltre && obj.libelle !== objectifFiltre) {
                    return;
                }
                if (!(obj.libelle in reussisParObjectif)) {
                    reussisParObjectif[obj.libelle] = false;
                }
                if (obj.reussi) {
                    reussisParObjectif[obj.libelle] = true;
                }
            });
        });

        // Chaque objectif tenté par cet apprenti est comptabilisé
        Object.keys(reussisParObjectif).forEach(function (libelle) {
            if (!objectifsMap[libelle]) {
                objectifsMap[libelle] = { reussi: 0, echoue: 0, nonTente: 0 };
            }
            if (reussisParObjectif[libelle]) {
                objectifsMap[libelle].reussi++;
            } else {
                objectifsMap[libelle].echoue++;
            }
        });
    });

    const total      = apprentis.length;
    const categories = Object.keys(objectifsMap);

    // nonTente = apprentis n'ayant aucune session avec cet objectif
    categories.forEach(function (libelle) {
        const tentes = objectifsMap[libelle].reussi + objectifsMap[libelle].echoue;
        objectifsMap[libelle].nonTente = Math.max(0, total - tentes);
    });

    const dataReussi   = categories.map(function (k) { return objectifsMap[k].reussi;    });
    const dataEchoue   = categories.map(function (k) { return objectifsMap[k].echoue;    });
    const dataNonTente = categories.map(function (k) { return objectifsMap[k].nonTente;  });

    console.log('Objectifs  :', categories);
    console.log('Réussis    :', dataReussi);
    console.log('Échoués    :', dataEchoue);
    console.log('Non tentés :', dataNonTente);

    const titre = objectifFiltre
        ? 'Réussite : ' + objectifFiltre
        : 'Réussite par objectif';

    const sousTitre = objectifFiltre
        ? "Réussi / Échoué / Non tenté pour cet objectif (" + total + ' apprentis)'
        : "Réussi / Échoué / Non tenté par objectif (" + total + ' apprentis)';

    Highcharts.chart('chartContainer', {
        chart: {
            type               : 'column',
            backgroundColor    : 'transparent',
            plotBackgroundColor: 'transparent',
            style              : { fontFamily: 'Raleway, sans-serif' }
        },
        navigation: {
            buttonOptions: {
                enabled: false
            }
        }, 
        title: {
            text : titre,
            style: { fontWeight: '600', fontSize: '1rem' }
        },
        subtitle: {
            text : sousTitre,
            style: { fontSize: '13px' }
        },
        xAxis: {
            categories: categories,
            labels    : { style: { fontSize: '13px', fontWeight: 'bold' } }
        },
        yAxis: {
            min           : 0,
            title         : { text: 'Pourcentage (%)' },
            reversedStacks: false
        },
        tooltip: {
            shared   : true,
            formatter: function () {
                let s = '<b>' + this.x + '</b><br/>';
                // Ordre d'affichage inversé = cohérent avec l'ordre visuel (haut → bas)
                this.points.slice().reverse().forEach(function (point) {
                    s += '<span style="color:' + point.color + '">●</span> '
                        + point.series.name + ' : <b>' + point.y + '</b> ('
                        + Highcharts.numberFormat(point.percentage, 0) + '%)'
                        + ' / ' + total + ' apprentis<br/>';
                });
                return s;
            }
        },
        plotOptions: {
            column: {
                stacking   : 'percent',
                borderWidth: 0,
                dataLabels : {
                    enabled: true,
                    format : '{point.percentage:.0f}%',
                    style  : { fontSize: '11px', fontWeight: 'bold', textOutline: 'none' }
                }
            }
        },
        series: [
            { name: 'Réussi',     data: dataReussi,   color: '#22c55e' },
            { name: 'Échoué',     data: dataEchoue,   color: '#ef4444' },            
            { name: 'Non tenté',  data: dataNonTente, color: '#9ca3af' }
        ],
        legend : { enabled: true, align: 'center', verticalAlign: 'bottom' },
        credits: { enabled: false }
    });
}