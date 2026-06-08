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

    const total = data.total;                                 // ← lecture directe
    const categories = data.objectifs.map(objectifs => objectifs.libelle);        // ← nom de l'objectif (libelle) pour les catégories de l'axe X
    const dataReussi = data.objectifs.map(objectifs => objectifs.reussi);         // ← nombre d'apprentis réussis pour chaque objectif
    const dataEchoue = data.objectifs.map(objectifs => objectifs.echoue);         // ← nombre d'apprentis échoués pour chaque objectif
    const dataNonTente = data.objectifs.map(objectifs => objectifs.non_tente);      // ← nombre d'apprentis non tentés pour chaque objectif

    console.log('Objectifs  :', categories);
    console.log('Réussis    :', dataReussi);
    console.log('Échoués    :', dataEchoue);
    console.log('Non tentés :', dataNonTente);

    const titre = 'Réussite par objectif';                       // ← filtre supprimé (géré côté serveur)
    const sousTitre = 'Réussi / Échoué / Non tenté par objectif (' + total + ' apprentis)';

    Highcharts.chart('chartContainer', {
        chart: {
            type: 'column',
            backgroundColor: 'transparent',
            plotBackgroundColor: 'transparent',
            style: { fontFamily: 'Raleway, sans-serif' }
        },
        navigation: {
            buttonOptions: {
                enabled: false
            }
        },
        title: {
            text: titre,
            style: { fontWeight: '600', fontSize: '1rem' }
        },
        subtitle: {
            text: sousTitre,
            style: { fontSize: '13px' }
        },
        xAxis: {
            categories: categories,
            labels: { style: { fontSize: '13px', fontWeight: 'bold' } }
        },
        yAxis: {
            min: 0,
            title: { text: 'Pourcentage (%)' },
            reversedStacks: false
        },
        tooltip: {
            shared: true,
            formatter: function () {

                // Conteneur principal du tooltip
                var $tooltip = $('<div>');

                // Titre (nom de l'objectif)
                $tooltip.append(
                    $('<b>').text(categories[this.x])
                );

                $tooltip.append('<br>');

                
                $.each(this.points.slice().reverse(), function (index, point) {

                    var $ligne = $('<div>');

                    $ligne.append(
                        $('<span>')
                            .css('color', point.color)
                            .text('● ')
                    );

                    $ligne.append(
                        point.series.name +
                        ' : '
                    );

                    $ligne.append(
                        $('<b>').text(point.y)
                    );

                    $tooltip.append('<br>');
                    
                    $ligne.append(
                        ' (' +
                        Highcharts.numberFormat(point.percentage, 0) +
                        '%) / ' +
                        total +
                        ' apprentis'
                    );

                    $tooltip.append($ligne);
                });

                return $tooltip.html();
            }
        },
        plotOptions: {
            column: {
                stacking: 'percent',
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.percentage:.0f}%',
                    style: { fontSize: '11px', fontWeight: 'bold', textOutline: 'none' }
                }
            }
        },
        series: [
            { name: 'Réussi', data: dataReussi, color: '#22c55e' },
            { name: 'Échoué', data: dataEchoue, color: '#ef4444' },
            { name: 'Non tenté', data: dataNonTente, color: '#9ca3af' }
        ],
        legend: { enabled: true, align: 'center', verticalAlign: 'bottom' },
        credits: { enabled: false }
    });
}