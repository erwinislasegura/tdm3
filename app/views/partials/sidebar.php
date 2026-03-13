<aside class="sidebar p-3">
  <div class="brand mb-4"><i class="fa-solid fa-table-tennis-paddle-ball"></i> TDM Pro</div>
  <a href="/admin/dashboard"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
  <a href="/admin/users"><i class="fa-solid fa-users-gear"></i> Usuarios</a>
  <a href="/admin/organizations"><i class="fa-solid fa-building"></i> Organizaciones</a>
  <a href="/admin/players"><i class="fa-solid fa-user-group"></i> Jugadores</a>
  <a href="/admin/tournaments"><i class="fa-solid fa-trophy"></i> Torneos</a>
  <a href="/admin/registrations"><i class="fa-solid fa-clipboard-check"></i> Inscripciones</a>
  <a href="/admin/matches"><i class="fa-solid fa-stopwatch"></i> Partidos</a>
  <a href="/admin/rankings"><i class="fa-solid fa-ranking-star"></i> Rankings</a>
  <?php if (can('root')): ?>
  <a href="/admin/audit-logs"><i class="fa-solid fa-shield"></i> Auditoría</a>
  <a href="/admin/settings"><i class="fa-solid fa-sliders"></i> Configuración</a>
  <?php endif; ?>
</aside>
