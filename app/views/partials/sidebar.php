<aside class="sidebar p-3">
  <div class="brand mb-4"><i class="fa-solid fa-table-tennis-paddle-ball"></i> TDM Pro</div>
  <a href="/admin/dashboard"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
  <?php if (has_permission('users.manage')): ?><a href="/admin/users"><i class="fa-solid fa-users-gear"></i> Usuarios</a><?php endif; ?>
  <?php if (has_permission('tournament.manage')): ?><a href="/admin/organizations"><i class="fa-solid fa-building"></i> Organizaciones</a><?php endif; ?>
  <?php if (has_permission('tournament.manage')): ?><a href="/admin/players"><i class="fa-solid fa-user-group"></i> Jugadores</a><?php endif; ?>
  <?php if (has_permission('tournament.manage')): ?><a href="/admin/tournaments"><i class="fa-solid fa-trophy"></i> Torneos</a><?php endif; ?>
  <?php if (has_permission('tournament.manage')): ?><a href="/admin/registrations"><i class="fa-solid fa-clipboard-check"></i> Inscripciones</a><?php endif; ?>
  <?php if (has_permission('matches.view')): ?><a href="/admin/matches"><i class="fa-solid fa-stopwatch"></i> Partidos</a><?php endif; ?>
  <?php if (has_permission('live.view')): ?><a href="/admin/live"><i class="fa-solid fa-tower-broadcast"></i> Live Center</a><?php endif; ?>
  <?php if (has_permission('groups.view')): ?><a href="/admin/competition-formats"><i class="fa-solid fa-object-group"></i> Grupos y Knockout</a><?php endif; ?>
  <?php if (has_permission('matches.view')): ?><a href="/admin/rankings"><i class="fa-solid fa-ranking-star"></i> Rankings</a><?php endif; ?>
  <?php if (has_permission('audit.view')): ?><a href="/admin/audit-logs"><i class="fa-solid fa-shield"></i> Auditoría</a><?php endif; ?>
  <?php if (has_permission('settings.manage')): ?><a href="/admin/settings"><i class="fa-solid fa-sliders"></i> Configuración</a><?php endif; ?>
</aside>
