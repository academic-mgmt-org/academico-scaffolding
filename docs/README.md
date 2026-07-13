# Interfaces desacopladas por core asset

Cada documento crea una aplicación Livewire independiente. El instalador
genera la estructura con Artisan, compila los clientes con `protoc` y aplica
solamente el parche común y el parche del servicio elegido.

| Core asset | Receta independiente | Ruta funcional |
|---|---|---|
| Autenticación y sesiones | [academico-login.md](academico-login.md) | `/academico/sesiones` |
| Gestión de usuarios | [academico-usuarios.md](academico-usuarios.md) | `/academico/usuarios` |
| Matrículas | [academico-matriculas.md](academico-matriculas.md) | `/academico/matriculas` |
| Calificaciones | [academico-calificaciones.md](academico-calificaciones.md) | `/academico/calificaciones` |
| Solicitudes académicas | [academico-solicitudes.md](academico-solicitudes.md) | `/academico/solicitudes` |
| Notificaciones | [academico-notificaciones.md](academico-notificaciones.md) | `/academico/notificaciones` |

Las cinco interfaces funcionales utilizan además el contrato de
`academico-login` para iniciar y mantener una sesión segura. Esa es una
dependencia técnica; no instala la interfaz funcional de ningún otro core
asset. El instalador rechaza mezclar dos módulos funcionales en la misma
aplicación.

Para auditar una selección sin modificar archivos:

```bash
/home/azureuser/academico-scaffolding/setup/install-interface-module.sh \
  academico-notificaciones \
  /ruta/a/la/aplicacion \
  --plan
```
