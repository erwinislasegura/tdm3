const chart = document.getElementById('dashboardChart');
if (chart) {
  new Chart(chart, {
    type: 'line',
    data: {labels: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'], datasets: [{label: 'Partidos', data: [12, 19, 11, 25, 18, 30, 22], borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,.2)', tension: .35, fill: true}]},
    options: {plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true}}}
  });
}
