# Sistema de Gestión - Taller Mecánico (CodeIgniter 4)

## Estado actual del proyecto

Este sistema está desarrollado en CodeIgniter 4 y sigue una arquitectura modular y escalable, ideal para la gestión de un taller mecánico. Se han implementado buenas prácticas para separar la lógica de cada módulo, centralizar recursos y facilitar el mantenimiento.

---

## Estructura de carpetas y archivos principales

```
fast/
├── app/
│   ├── Commands/
│   ├── Config/
│   ├── Controllers/
│   │   ├── Auth.php
│   │   ├── BaseController.php
│   │   ├── Dashboard.php
│   │   ├── Home.php
│   │   ├── Usuarios.php
│   │   ├── Clientes.php
│   │   ├── Vehiculos.php
│   │   ├── Repuestos.php
│   │   ├── Ordenes.php
│   │   └── Reportes.php
│   ├── Filters/
│   │   ├── AuthFilter.php
│   │   └── AdminFilter.php
│   ├── Models/
│   │   ├── UsuarioModel.php
│   │   ├── ClienteModel.php
│   │   ├── VehiculoModel.php
│   │   ├── RepuestoModel.php
│   │   ├── OrdenModel.php
│   │   └── ReporteModel.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   └── main.php
│   │   ├── components/
│   │   │   ├── navbar.php
│   │   │   └── footer.php
│   │   ├── dashboard/
│   │   │   └── home.php
│   │   ├── usuarios/
│   │   │   ├── index.php
│   │   │   ├── crear.php
│   │   │   └── editar.php
│   │   ├── clientes/
│   │   │   └── index.php
│   │   ├── vehiculos/
│   │   │   └── index.php
│   │   ├── repuestos/
│   │   │   └── index.php
│   │   ├── ordenes/
│   │   │   └── index.php
│   │   └── reportes/
│   │       └── index.php
│   └── ...
├── public/
│   └── assets/
│       ├── css/
│       │   ├── custom.css
│       │   ├── usuarios.css
│       │   ├── clientes.css
│       │   ├── vehiculos.css
│       │   ├── repuestos.css
│       │   ├── ordenes.css
│       │   └── reportes.css
│       └── js/
│           ├── main.js
│           ├── usuarios.js
│           ├── clientes.js
│           ├── vehiculos.js
│           ├── repuestos.js
│           ├── ordenes.js
│           └── reportes.js
├── app/Config/Routes.php
├── app/Config/Filters.php
└── ...
```

---

## Implementación y buenas prácticas

- **Arquitectura modular:** Cada módulo (usuarios, clientes, vehículos, repuestos, órdenes, reportes) tiene su propio controlador, modelo, carpeta de vistas, JS y CSS.
- **Filtros de seguridad:**
  - `AuthFilter`: Protege rutas que requieren usuario autenticado.
  - `AdminFilter`: Solo permite acceso a usuarios con rol administrador (id_rol = 1).
  - Las rutas de usuarios y reportes solo pueden ser accedidas por administradores.
- **Recursos estáticos centralizados:**
  - Archivos JS y CSS globales y específicos por módulo.
  - Inclusión automática de CSS según el segmento de la URL.
- **Vistas limpias y reutilizables:**
  - Layout principal (`main.php`) con navbar y footer.
  - Cada módulo tiene su propia carpeta de vistas.
- **Escalabilidad:**
  - Estructura lista para agregar nuevos módulos siguiendo el mismo patrón.
  - Fácil mantenimiento y extensión.

---

## Próximos pasos sugeridos

- Implementar CRUD completo para cada módulo.
- Mejorar validaciones y mensajes de error.
- Agregar migraciones y seeds para la base de datos.
- Desarrollar reportes y estadísticas visuales.
- Documentar endpoints y lógica de negocio.

---

**Desarrollado por el equipo del Taller Rápido y Furioso.** 