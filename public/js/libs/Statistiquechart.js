/*
function initChart(apprentis, objectifFiltre = null) {
[...ancien code commenté conservé...]
}*/

/**
 * Statistiquechart.js
 * Bar chart Highcharts — Réussite par objectif (colonnes droites, stacking %).
 * - Fond totalement transparent (hérite du site)
 * - Échoué (rouge) EN HAUT
 * - Réussi (vert) AU MILIEU
 * - Non tenté (gris) EN BAS
 *
 * Reçoit : { total, objectifs: [{libelle, reussi, echoue, non_tente}] }
 */
function initChart(data) {                                          // ← signature changée

    if (!data || !data.objectifs || data.objectifs.length === 0 || !document.getElementById('chartContainer')) {
        return;
    }

    const total       = data.total;                                 // ← lecture directe
    const categories  = data.objectifs.map(o => o.libelle);        // ← lecture directe
    const dataReussi  = data.objectifs.map(o => o.reussi);         // ← lecture directe
    const dataEchoue  = data.objectifs.map(o => o.echoue);         // ← lecture directe
    const dataNonTente= data.objectifs.map(o => o.non_tente);      // ← lecture directe (non_tente avec _)

    console.log('Objectifs  :', categories);
    console.log('Réussis    :', dataReussi);
    console.log('Échoués    :', dataEchoue);
    console.log('Non tentés :', dataNonTente);

    const titre    = 'Réussite par objectif';                       // ← filtre supprimé (géré côté serveur)
    const sousTitre = 'Réussi / Échoué / Non tenté par objectif (' + total + ' apprentis)';

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
            { name: 'Réussi',    data: dataReussi,   color: '#22c55e' },
            { name: 'Échoué',    data: dataEchoue,   color: '#ef4444' },
            { name: 'Non tenté', data: dataNonTente, color: '#9ca3af' }
        ],
        legend : { enabled: true, align: 'center', verticalAlign: 'bottom' },
        credits: { enabled: false }
    });
}