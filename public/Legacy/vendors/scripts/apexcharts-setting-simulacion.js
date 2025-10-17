document.addEventListener('DOMContentLoaded', function () {
  // Obtener los valores de los elementos
  let valorCapitalTexto = document.getElementById('valor-capital').innerText;
  let valorIndustriaTexto = document.getElementById('valor-industria').innerText;


  let participacion_maxima = parseFloat(document.getElementById('max').innerText.replace('%', '').replace(',', '.'));
  let participacion_minima = parseFloat(document.getElementById('min').innerText.replace('%', '').replace(',', '.'));
  

  // Función para limpiar la cadena y convertirla en número entero
  function convertirANumero(valor) {
    return parseInt(valor.replace(/[\$,.\s]/g, ''));
  }

  // Convertir los valores a números enteros
  let valorCapital = convertirANumero(valorCapitalTexto);
  let valorIndustria = convertirANumero(valorIndustriaTexto);

  // Paso 2: Leer desde el localStorage en JavaScript
  var cantidadUsuarios = localStorage.getItem('cantidadUsuarios');
  
  var usuariosCapital = localStorage.getItem('usuariosCapital');
  var usuariosIndustria = localStorage.getItem('usuariosIndustria');

  var inversionTipo1 = parseInt(localStorage.getItem('inversionTipo1'));
  var inversionTipo2 = parseInt(localStorage.getItem('inversionTipo2'));
  var inversionTipo3 = parseInt(localStorage.getItem('inversionTipo3'));
  let total = inversionTipo1 + inversionTipo2 + inversionTipo3;

  let total_ci = usuariosCapital+usuariosIndustria;

  // Calcular los porcentajes
  inversionTipo1 = Math.trunc((inversionTipo1 / total) * 100);
  inversionTipo2 = Math.trunc((inversionTipo2 / total) * 100);
  inversionTipo3 = Math.trunc((inversionTipo3 / total) * 100);





 

  // Configuración del gráfico de pastel (Pie Chart)
  const tbody = document.querySelector('#data-table-body');
  const rows = tbody.querySelectorAll('tr');

  // Inicializar arrays para los datos
  let data = [];
  let labels = [];

  // Recorrer las filas para extraer los porcentajes y las etiquetas
  rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    const porcentaje = parseFloat(cells[4].textContent.replace(',', '.').replace('%', '')); // Extraer y limpiar porcentaje
    const nombre = cells[0].textContent;

    if (!isNaN(porcentaje)) {
      labels.push(nombre);
      data.push(porcentaje);
    }
  });
  var options = {
    series: [{
      data: data
    }],
    chart: {
      height: 350,
      type: 'bar',
      events: {
        click: function (chart, w, e) {
          // console.log(chart, w, e)
        }
      }
    },
    plotOptions: {
      bar: {
        columnWidth: '45%',
        distributed: true,
      }
    },
    dataLabels: {
      enabled: false
    },
    legend: {
      show: false
    },
    xaxis: {
      categories: labels,
      labels: {
        style: {
          fontSize: '12px'
        }
      }
    }
  };

  var chart = new ApexCharts(document.querySelector("#barras"), options);
  chart.render();



  var options1 = {
    series: [valorCapital, valorIndustria],
    chart: {
      width: 380,
      type: 'pie',
    },
    colors: ['#4472C4', '#954F72'],
    labels: ['Aportes Capital', 'Aportes Industria'],
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 150
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  };
  var chart1 = new ApexCharts(document.querySelector("#chart8Valor"), options1);
  chart1.render();

  // Configuración del gráfico de líneas (Line Chart)
  var proyectoId = document.querySelector('select[name="proyecto"]').value;

// Función para cargar los datos desde el archivo PHP
fetch('datosLine.php?proyecto=' + encodeURIComponent(proyectoId))
  .then(response => {
    if (!response.ok) {
      throw new Error('Error en la solicitud: ' + response.statusText);
    }
    return response.text(); // Obtener la respuesta como texto
  })
  .then(text => {
    
    try {
      // Intentar analizar la respuesta como JSON
      const datosAnuales = JSON.parse(text);
      
  var options = {
    series: [{
    name: 'Dinero',
    type: 'column',
    data: datosAnuales.dinero
  }, {
    name: 'Especie',
    type: 'area',
    data: datosAnuales.especie
  }, {
    name: 'Industria',
    type: 'line',
    data: datosAnuales.industria
  }],
    chart: {
    height: 350,
    type: 'line',
    stacked: false,
  },
  stroke: {
    width: [0, 2, 5],
    curve: 'smooth'
  },
  plotOptions: {
    bar: {
      columnWidth: '50%'
    }
  },
  
  fill: {
    opacity: [0.85, 0.25, 1],
    gradient: {
      inverseColors: false,
      shade: 'light',
      type: "vertical",
      opacityFrom: 0.85,
      opacityTo: 0.55,
      stops: [0, 100, 100, 100]
    }
  },
  labels: ['2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030'],
  markers: {
    size: 0
  },
  
  yaxis: {
    title: {
      text: 'Valor ajustado',
    }
  },
  tooltip: {
    shared: true,
    intersect: false,
    y: {
      formatter: function (y) {
        if (typeof y !== "undefined") {
          return y.toFixed(0) + " COP";
        }
        return y;
  
      }
    }
  }
  };

  var chart = new ApexCharts(document.querySelector("#TimeLine"), options);
  chart.render();
} catch (e) {
  console.error('Error al analizar JSON:', e);
}
})
.catch(error => console.error('Error al cargar los datos:', error));


  // Configuración del gráfico de área polar (Polar Area Chart)
  var options2 = {
    series: [inversionTipo1, inversionTipo2, inversionTipo3],
    chart: {
      width: 370,
      type: 'polarArea',
    },
    stroke: {
      colors: ['#fff']
    },
    fill: {
      opacity: 0.8
    },
    colors: ['#607D8B', '#FFC107', '#4CAF50'],
    labels: ['Dinero', 'Especie', 'Industria'],
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 100
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  };
  var chart3 = new ApexCharts(document.querySelector("#chart8Tipos"), options2);
  chart3.render();

  // Configuración del gráfico radial (Radial Bar Chart)
  var options9 = {
    series: [participacion_maxima, participacion_minima],
    chart: {
      height: 250,
      type: 'radialBar',
    },
    plotOptions: {
      radialBar: {
        offsetY: 0,
        startAngle: 0,
        endAngle: 270,
        hollow: {
          margin: 5,
          size: '40%',
          background: 'transparent',
          image: undefined,
        },
        dataLabels: {
          name: {
            show: false,
          },
          value: {
            show: false,
          }
        }
      }
    },
    colors: ['#1ab7ea', '#0084ff'],
    labels: ['Máxima', 'Minima'],
    legend: {
      show: true,
      floating: true,
      fontSize: '14px',
      position: 'left',
      offsetX: 40,
      offsetY: 15,
      labels: {
        useSeriesColors: true,
      },
      markers: {
        size: 0
      },
      formatter: function (seriesName, opts) {
        return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]
      },
      itemMargin: {
        vertical: 3
      }
    },
    responsive: [{
      breakpoint: 480,
      options: {
        legend: {
          show: false
        }
      }
    }]
  };
  var chart4 = new ApexCharts(document.querySelector("#chart9"), options9);
  chart4.render();
  var options = {
    series: [cantidadUsuarios],
    chart: {
      height: 300,
      type: 'radialBar',

    },
    plotOptions: {
      radialBar: {
        startAngle: -135,
        endAngle: 225,
        hollow: {
          margin: 0,
          size: '70%',
          background: '#fff',
          image: undefined,
          imageOffsetX: 0,
          imageOffsetY: 0,
          position: 'front',
          dropShadow: {
            enabled: true,
            top: 3,
            left: 0,
            blur: 4,
            opacity: 0.24
          }
        },
        track: {
          background: '#fff',
          strokeWidth: '67%',
          margin: 0, // margin is in pixels
          dropShadow: {
            enabled: true,
            top: -3,
            left: 0,
            blur: 4,
            opacity: 0.35
          }
        },

        dataLabels: {
          show: true,
          name: {
            offsetY: -10,
            show: true,
            color: '#888',
            fontSize: '17px'
          },
          value: {
            formatter: function (val) {
              return parseInt(val);
            },
            color: '#111',
            fontSize: '36px',
            show: true,
          }
        }
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'dark',
        type: 'horizontal',
        shadeIntensity: 0.5,
        gradientToColors: ['#ABE5A1'],
        inverseColors: true,
        opacityFrom: 1,
        opacityTo: 1,
        stops: [0, 100]
      }
    },
    stroke: {
      lineCap: 'round'
    },
    labels: ['Usuarios'],
  };

  var chart = new ApexCharts(document.querySelector("#velocimetro"), options);
  chart.render();
});