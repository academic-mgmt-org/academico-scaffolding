# Interfaz independiente: academico-matriculas

Esta receta instala únicamente Matrículas: inscripción, consulta, cambios de
estado, cancelación y relación con asignaturas. Auth se compila solo para la
sesión; no se instalan Usuarios, Calificaciones, Solicitudes ni Notificaciones.
Los identificadores de estudiante u oferta requeridos se ingresan en la propia
interfaz, por lo que no es necesario implementar primero otro módulo.

## Crear e instalar

```bash
# ===== INICIO: INTERFAZ SOLO PARA ACADEMICO-MATRICULAS =====
set -Eeuo pipefail

SCAFFOLDING_DIR='/home/azureuser/academico-scaffolding'
WORK_ROOT="${WORK_ROOT:-$HOME/interfaces-academicas}"
APP_NAME="${APP_NAME:-interfaz-academico-matriculas}"
LIVEWIRE_STARTER='laravel/livewire-starter-kit:dev-main#1f84e33e6bf6c95f9925e3e023bce71341ced005'

cd "$SCAFFOLDING_DIR"
./setup/install-interface-dependencies.sh

mkdir -p "$WORK_ROOT"
cd "$WORK_ROOT"
laravel new "$APP_NAME" \
  --using="$LIVEWIRE_STARTER" \
  --phpunit \
  --database=sqlite \
  --npm \
  --no-boost \
  --no-interaction

"$SCAFFOLDING_DIR/setup/install-interface-module.sh" \
  academico-matriculas \
  "$WORK_ROOT/$APP_NAME"

"$SCAFFOLDING_DIR/setup/prepare-interface-runtime.sh" \
  "$WORK_ROOT/$APP_NAME"
# ===== FIN: INTERFAZ SOLO PARA ACADEMICO-MATRICULAS =====
```

El runtime también se genera: Sail crea `compose.yaml` y el preparador construye
la capa PHP 8.5 con la extensión gRPC antes de ejecutar migraciones y Vite.

El resultado compila exactamente `auth_v1.proto` y `matriculas_v1.proto`. La
pantalla de acceso está en `/academico/login` y el flujo de
`MATRICULAS_INSCRIPCION_AJUSTE_CANCELACION.md` queda en
`/academico/matriculas`.

## Ejecutar y comprobar

```bash
cd "${WORK_ROOT:-$HOME/interfaces-academicas}/${APP_NAME:-interfaz-academico-matriculas}"
find proto -maxdepth 1 -type f -printf '%f\n' | sort
docker compose up -d --no-build
docker compose exec -T laravel.test php artisan route:list --name=academic
```

El host predeterminado puede sustituirse con `GATEWAY_GRPC_HOST` en `.env`; si
el gateway usa TLS, definir `GATEWAY_GRPC_TLS=true`.
