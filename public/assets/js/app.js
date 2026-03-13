const chart = document.getElementById('dashboardChart');
if (chart) {
  new Chart(chart, {
    type: 'line',
    data: {labels: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'], datasets: [{label: 'Partidos', data: [12, 19, 11, 25, 18, 30, 22], borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,.2)', tension: .35, fill: true}]},
    options: {plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true}}}
  });
}

const liveBoard = document.querySelector('[data-live-board]');
if (liveBoard) {
  const endpoint = liveBoard.dataset.endpoint;
  const updatedBadge = liveBoard.querySelector('[data-live-updated]');
  const matchesBody = liveBoard.querySelector('[data-live-matches] tbody');
  const bracketsBody = liveBoard.querySelector('[data-live-brackets] tbody');
  const standingsBody = liveBoard.querySelector('[data-live-standings] tbody');

  const statusBadge = (value) => {
    const map = { in_progress: 'text-bg-danger', scheduled: 'text-bg-secondary', finished: 'text-bg-success' };
    const klass = map[value] || 'text-bg-dark';
    return `<span class="badge ${klass}">${value}</span>`;
  };

  const render = (payload) => {
    updatedBadge.textContent = `Última actualización: ${payload.updated_at}`;

    matchesBody.innerHTML = payload.in_progress.map((m) => {
      const sets = (m.sets || []).map((s) => `${s.player_a_points}-${s.player_b_points}`).join(' | ') || '-';
      return `<tr>
        <td><strong>${m.player_a_name}</strong> vs <strong>${m.player_b_name}</strong><div class="small text-muted">${m.tournament_name} · ${m.phase}</div></td>
        <td>${m.table_number ?? '-'}</td>
        <td>${statusBadge(m.status)}</td>
        <td>${sets}</td>
        <td>${m.winner_name ?? '-'}</td>
      </tr>`;
    }).join('') || '<tr><td colspan="5" class="text-muted">Sin partidos activos</td></tr>';

    bracketsBody.innerHTML = payload.active_bracket_matches.map((b) => `<tr>
      <td><strong>${b.tournament_name}</strong><div class="small text-muted">${b.bracket_name} · ${b.player_a_name ?? 'TBD'} vs ${b.player_b_name ?? 'TBD'}</div></td>
      <td>R${b.round_number}</td>
      <td>#${b.match_number}</td>
      <td>${statusBadge(b.status)}</td>
    </tr>`).join('') || '<tr><td colspan="4" class="text-muted">Sin llaves activas</td></tr>';

    standingsBody.innerHTML = payload.group_standings.map((s) => `<tr>
      <td>${s.tournament_name}</td><td>${s.group_name}</td><td>${s.position}</td><td>${s.player_name}</td><td>${s.match_points}</td><td>${s.won}-${s.lost}</td>
    </tr>`).join('') || '<tr><td colspan="6" class="text-muted">Sin datos de tabla de grupos</td></tr>';
  };

  const fetchLive = async () => {
    const response = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!response.ok) return;
    const payload = await response.json();
    render(payload);
  };

  render(window.__LIVE_BOOTSTRAP__ || { in_progress: [], active_bracket_matches: [], group_standings: [], updated_at: '-' });
  fetchLive();
  setInterval(fetchLive, 8000);
}
