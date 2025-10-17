document.addEventListener('DOMContentLoaded', function() {
	let valorCapital = parseFloat(document.getElementById('valor-capital').innerText.replace(/\s|\$/g, ''));
	let valorIndustria = parseFloat(document.getElementById('valor-industria').innerText.replace(/\s|\$/g, ''));
var options = {
	series: [valorCapital,valorIndustria],
	chart: {
	width: 380,
	type: 'pie',
  },
  labels: ['Aportes Capital', 'Aportes Industria'],
  responsive: [{
	breakpoint: 480,
	options: {
	  chart: {
		width: 200
	  },
	  legend: {
		position: 'bottom'
	  }
	}
  }]
};
  var chart = new ApexCharts(document.querySelector("#chart8Valor"), options);
  chart.render();
});