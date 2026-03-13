# Módulo de Fase de Grupos + Knockout (ITTF)

## Reglas implementadas
- Distribución de grupos por **snake system** (A..N y retorno inverso).
- Puntos ITTF en grupos:
  - victoria: 2
  - derrota jugada: 1
  - derrota por walkover: 0
- Criterio de desempate operativo:
  1. Match points
  2. Ratio de games
  3. Ratio de puntos
  4. Trazabilidad para desempate manual (configurable)
- Cierre de grupos con validación de partidos finalizados.
- Clasificación automática (ganadores / top2 / mejores terceros).
- Generación de knockout con tamaño potencia de 2 + byes.
- Protección de seeds en posiciones principales (1 arriba, 2 abajo, 3/4 en mitades opuestas).
- Separación por club/asociación/federación con swaps mínimos cuando es posible.

## Trazabilidad
- Se registra en `draw_logs` la generación de grupos, swaps y advertencias.
- Se registra en `audit_logs` creación, cierre de grupos, scoring y generación de cuadro.

## Parametrización en admin
Desde configuración del formato:
- cantidad de grupos, tamaño, clasificados
- mejores terceros
- cantidad de seeds protegidos
- criterio de ranking
- regla de separación
- modo de generación (automático/manual asistido)

