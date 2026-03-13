# Relaciones clave

- Un `organization` tiene muchos `clubs`, `venues`, `tournaments`.
- Una `person` puede ser `player`, `coach` o `referee`.
- `registrations` vincula `players` con `tournaments` y categorías/modalidades.
- `groups` y `group_members` modelan round robin.
- `matches` se vincula a torneo, grupo opcional y árbitro.
- `rankings` guarda posición por organización/categoría/modalidad.
