document.addEventListener('DOMContentLoaded', function() {
	// Obtener los valores de los elementos
let total = document.getElementById('Valor-Total').innerText;
const tbody = document.querySelector('#data-table-body');
    const rows = tbody.querySelectorAll('tr');

    // Inicializar array para los datos
    let chartData = [];

    // Recorrer las filas para extraer los porcentajes y las etiquetas
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const porcentaje = parseFloat(cells[4].textContent.replace(',', '.').replace('%', '')); // Extraer y limpiar porcentaje
        const nombre = cells[0].textContent;

        if (!isNaN(porcentaje)) {
            chartData.push({ name: nombre, y: porcentaje });
        }
    });
Highcharts.chart('chart6', {
    chart: {
        type: 'pie',
        custom: {disableMoreOptions: true},
        events: {
            load() {
                const chart = this,
                    series = chart.series[0];
                let customLabel = chart.options.chart.custom.label;

                if (!customLabel) {
                    customLabel = chart.options.chart.custom.label =
                        chart.renderer.label(
                            'Total<br/>' + total
                        )
                            .css({
                                color: '#000066',
                                textAnchor: 'middle'
                            })
                            .add();
                }

                const x = series.center[0] + chart.plotLeft,
                    y = series.center[1] + chart.plotTop -
                    (customLabel.attr('height') / 2);

                customLabel.attr({
                    x,
                    y
                });
                // Set font size based on chart diameter
                customLabel.css({
                    fontSize: `${series.center[2] / 12}px`
                });
            }
        }
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    title: {
        text: 'Participación accionaria'
    },
    subtitle: {
        text: 'Porcentajes por inversionistas'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.0f}%</b>'
    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            allowPointSelect: true,
            cursor: 'pointer',
            borderRadius: 8,
            dataLabels: [{
                enabled: true,
                distance: 20,
                format: '{point.name}'
            }, {
                enabled: true,
                distance: -15,
                format: '{point.percentage:.0f}%',
                style: {
                    fontSize: '0.9em'
                }
            }],
            showInLegend: true
        }
    },
    series: [{
        name: 'Porcentaje de participación',
        colorByPoint: true,
        innerSize: '75%',
        data:chartData   
    }]
});

});
