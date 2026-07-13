# Interfaces académicas desacopladas

Este repositorio genera una aplicación Laravel Livewire independiente por cada
core asset académico. No contiene una instalación monolítica: al seleccionar
un servicio se incorporan únicamente el núcleo técnico de sesión, Auth y el
módulo funcional elegido.

## Elegir una interfaz

Cada receta es autocontenida y puede ejecutarse sin consultar las demás:

| Core asset | Receta | Ruta resultante |
|---|---|---|
| Autenticación y sesiones | [`docs/academico-login.md`](docs/academico-login.md) | `/academico/sesiones` |
| Gestión de usuarios | [`docs/academico-usuarios.md`](docs/academico-usuarios.md) | `/academico/usuarios` |
| Matrículas | [`docs/academico-matriculas.md`](docs/academico-matriculas.md) | `/academico/matriculas` |
| Calificaciones | [`docs/academico-calificaciones.md`](docs/academico-calificaciones.md) | `/academico/calificaciones` |
| Solicitudes académicas | [`docs/academico-solicitudes.md`](docs/academico-solicitudes.md) | `/academico/solicitudes` |
| Notificaciones | [`docs/academico-notificaciones.md`](docs/academico-notificaciones.md) | `/academico/notificaciones` |

Para Notificaciones, por ejemplo, basta con ejecutar el bloque de
[`docs/academico-notificaciones.md`](docs/academico-notificaciones.md). El
resultado no contiene clientes, configuración ni flujos de Usuarios,
Matrículas, Calificaciones o Solicitudes.

## Cómo se genera

Los archivos estructurales no se mantienen como copias manuales. El proceso es:

1. Laravel Installer crea la aplicación desde el Starter Kit oficial de
   Livewire fijado en el commit
   `1f84e33e6bf6c95f9925e3e023bce71341ced005`.
2. `php artisan make:*` crea la interfaz PHP, servicio, excepción, proveedor,
   middleware, controladores, configuraciones, vistas y prueba.
3. `patches/0001-interface-core.patch` completa esos stubs con el
   cliente gRPC genérico y la sesión remota.
4. Se aplica exactamente uno de los seis parches de módulo.
5. Los `.proto` se obtienen de revisiones Git fijadas, se validan por SHA-256 y
   `protoc` genera los clientes PHP. Siempre se compila Auth; adicionalmente se
   compila solo el contrato funcional seleccionado.
6. Sail genera `compose.yaml` y se prepara PHP 8.5 con la extensión gRPC.

`academico-login` es una dependencia técnica de las otras cinco interfaces:
permite login, JWT, refresh y logout. Esto no instala otra interfaz funcional.

## Usar el selector en una aplicación ya creada

Para auditar la selección sin modificar archivos:

```bash
./setup/install-interface-module.sh \
  academico-notificaciones \
  /ruta/a/la-aplicacion \
  --plan
```

Para instalarla:

```bash
./setup/install-interface-module.sh \
  academico-notificaciones \
  /ruta/a/la-aplicacion

./setup/prepare-interface-runtime.sh \
  /ruta/a/la-aplicacion
```

La aplicación debe provenir de la revisión del Starter Kit indicada. El
instalador es reejecutable para el mismo módulo y rechaza mezclar otro core
asset en esa aplicación.

## Estructura del repositorio

```text
docs/                         una receta autocontenida por core asset
patches/                      núcleo común y seis parches mutuamente excluyentes
setup/install-interface-dependencies.sh
setup/install-interface-module.sh
setup/prepare-interface-runtime.sh
locks/                        locks reproducibles de Composer y npm
tests/modular-scaffolding.sh  auditoría del selector y del aislamiento
```

## Verificación

La comprobación estática del repositorio no necesita crear una aplicación:

```bash
./tests/modular-scaffolding.sh
```

Además, cada instalación ejecuta `AcademicInterfaceTest`. Esa prueba valida la
sesión, el allow-list de RPCs, el formulario de cada acción y que el módulo no
registre dominios funcionales ajenos.
