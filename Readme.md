# DOCUMENTACIÓN COMPLETA DEL PROYECTO
## SISTEMA DE GESTIÓN DE TALLER MECÁNICO

---

## **INFORMACIÓN GENERAL DEL PROYECTO**

### **Tecnologías Utilizadas**
- **Framework**: CodeIgniter 4.x
- **Lenguaje**: PHP 8.x
- **Base de Datos**: MySQL (XAMPP)
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5.3.5
- **Librerías**: DataTables, SweetAlert2, FontAwesome
- **Servidor**: Apache (XAMPP)

### **Estructura del Proyecto**
```
fast/
├── app/
│   ├── Controllers/     # Controladores de la aplicación
│   ├── Models/         # Modelos de datos
│   ├── Views/          # Vistas y plantillas
│   ├── Config/         # Configuración del framework
│   ├── Filters/        # Filtros de autenticación
│   ├── Helpers/        # Funciones auxiliares
│   └── Database/       # Migraciones y seeds
├── public/
│   ├── assets/
│   │   ├── css/        # Estilos personalizados
│   │   └── js/         # Scripts JavaScript
└── writable/           # Archivos temporales
```

---

## **FUNCIONALIDADES IMPLEMENTADAS**

### **1. SISTEMA DE AUTENTICACIÓN Y AUTORIZACIÓN**

#### **1.1 Gestión de Usuarios**
- **Funcionalidad**: CRUD completo de usuarios
- **Roles**: Administrador (rol 1) y Mecánico (rol 2)
- **Características**:
  - Registro de usuarios con validación
  - Edición de perfiles
  - Eliminación de usuarios
  - Gestión de roles y permisos
- **Complejidad**: Media-Alta
  - Validación de formularios
  - Encriptación de contraseñas
  - Control de acceso basado en roles

#### **1.2 Sistema de Login**
- **Funcionalidad**: Autenticación segura
- **Características**:
  - Formulario de login responsivo
  - Validación de credenciales
  - Redirección según rol
  - Protección de rutas
- **Complejidad**: Media
  - Manejo de sesiones
  - Filtros de autenticación

### **2. GESTIÓN DE CLIENTES**

#### **2.1 CRUD de Clientes**
- **Funcionalidad**: Gestión completa de clientes
- **Características**:
  - Crear, leer, actualizar, eliminar clientes
  - Validación de datos
  - Generación automática de correos electrónicos
  - Búsqueda y filtrado avanzado
- **Complejidad**: Media
  - Validación de formularios
  - Integración con DataTables
  - Notificaciones con SweetAlert2

#### **2.2 Generación Automática de Correos**
- **Funcionalidad**: Función `WP_FN_GENERAR_CORREO`
- **Características**:
  - Genera correos basados en nombre y apellido
  - Campo de email oculto en la interfaz
  - Integración con base de datos
- **Complejidad**: Baja-Media

### **3. GESTIÓN DE VEHÍCULOS**

#### **3.1 CRUD de Vehículos**
- **Funcionalidad**: Gestión de vehículos por cliente
- **Características**:
  - Registro de vehículos asociados a clientes
  - Información técnica completa
  - Relación cliente-vehículo
  - Validación de datos
- **Complejidad**: Media
  - Relaciones entre entidades
  - Validación de formularios

#### **3.2 Marcas y Modelos**
- **Funcionalidad**: Catálogo de marcas y modelos
- **Características**:
  - Gestión de marcas de vehículos
  - Modelos asociados a marcas
  - Integración con vehículos
- **Complejidad**: Baja

### **4. GESTIÓN DE REPUESTOS**

#### **4.1 CRUD de Repuestos**
- **Funcionalidad**: Gestión de inventario de repuestos
- **Características**:
  - Crear, editar, eliminar repuestos
  - Control de stock
  - Categorización de repuestos
  - Precios en formato entero
- **Complejidad**: Media
  - Control de inventario
  - Validación de stock

#### **4.2 Sistema de Permisos Granulares**
- **Funcionalidad**: Control de acceso por rol
- **Características**:
  - Administradores: Acceso completo
  - Mecánicos: Solo lectura
  - Botones y acciones ocultas según permisos
- **Complejidad**: Alta
  - Sistema de permisos personalizado
  - Control a nivel de vista y controlador

#### **4.3 Movimientos de Stock**
- **Funcionalidad**: Registro de movimientos
- **Características**:
  - Entradas y salidas de stock
  - Historial de movimientos
  - Motivos de ajuste
- **Complejidad**: Media
  - Auditoría de movimientos
  - Integración con repuestos

### **5. GESTIÓN DE ÓRDENES DE TRABAJO**

#### **5.1 CRUD de Órdenes**
- **Funcionalidad**: Gestión completa de órdenes
- **Características**:
  - Crear órdenes de trabajo
  - Asignar vehículos y clientes
  - Estados de órdenes (Pendiente, En Proceso, Completada, Cancelada)
  - Fechas de creación y actualización
- **Complejidad**: Alta
  - Relaciones complejas entre entidades
  - Estados y flujos de trabajo

#### **5.2 Gestión de Repuestos en Órdenes**
- **Funcionalidad**: Asociar repuestos a órdenes
- **Características**:
  - Agregar repuestos a órdenes
  - Cantidades utilizadas
  - Cálculo automático de costos
  - Descuento automático de stock
- **Complejidad**: Alta
  - Transacciones de base de datos
  - Cálculos automáticos
  - Control de inventario

#### **5.3 Impresión de Órdenes**
- **Funcionalidad**: Generar PDF de órdenes
- **Características**:
  - Formato profesional
  - Información completa de la orden
  - Repuestos utilizados
  - Costos detallados
- **Complejidad**: Media
  - Generación de PDF
  - Formato de impresión

### **6. SISTEMA DE REPORTES**

#### **6.1 Reportes de Órdenes**
- **Funcionalidad**: Múltiples tipos de reportes
- **Características**:
  - Órdenes por cliente
  - Órdenes por estado
  - Repuestos más utilizados
  - Total recaudado
  - Movimientos de stock
- **Complejidad**: Media-Alta
  - Consultas complejas
  - Agregaciones y filtros
  - Formato de reportes

#### **6.2 Dashboard**
- **Funcionalidad**: Panel de control
- **Características**:
  - Estadísticas generales
  - Resumen de actividades
  - Acceso rápido a funciones
- **Complejidad**: Baja

### **7. FUNCIONALIDADES AVANZADAS**

#### **7.1 Sistema de Alertas**
- **Funcionalidad**: Alertas de stock bajo
- **Características**:
  - Notificaciones automáticas
  - Umbrales configurables
  - Integración con repuestos
- **Complejidad**: Media
  - Triggers de base de datos
  - Sistema de notificaciones

#### **7.2 Validaciones y Seguridad**
- **Funcionalidad**: Múltiples capas de seguridad
- **Características**:
  - Validación de formularios
  - Protección CSRF
  - Filtros de autenticación
  - Control de acceso por roles
- **Complejidad**: Alta
  - Implementación de seguridad
  - Manejo de sesiones

---

## **COMPLEJIDAD TÉCNICA DEL PROYECTO**

### **Nivel de Complejidad: ALTO**

#### **Aspectos de Alta Complejidad:**

1. **Sistema de Permisos Granulares**
   - Control de acceso a nivel de función
   - Permisos dinámicos por rol
   - Ocultación condicional de elementos UI

2. **Gestión de Inventario**
   - Control automático de stock
   - Transacciones de base de datos
   - Alertas de stock bajo

3. **Relaciones Complejas**
   - Cliente → Vehículos → Órdenes → Repuestos
   - Múltiples entidades relacionadas
   - Integridad referencial

4. **Generación Automática**
   - Correos electrónicos automáticos
   - IDs generados por triggers
   - Cálculos automáticos de costos

#### **Aspectos de Complejidad Media:**

1. **Sistema de Reportes**
   - Consultas complejas
   - Agregaciones de datos
   - Múltiples filtros

2. **Validación y Seguridad**
   - Validación de formularios
   - Protección CSRF
   - Manejo de sesiones

3. **Interfaz de Usuario**
   - DataTables responsivos
   - Notificaciones SweetAlert2
   - Formularios dinámicos

#### **Aspectos de Baja Complejidad:**

1. **CRUD Básico**
   - Operaciones estándar
   - Validación simple
   - Interfaz básica

2. **Navegación**
   - Menú responsivo
   - Enlaces dinámicos
   - Breadcrumbs

---

## **CARACTERÍSTICAS TÉCNICAS AVANZADAS**

### **1. Arquitectura MVC**
- Separación clara de responsabilidades
- Código organizado y mantenible
- Reutilización de componentes

### **2. Base de Datos Relacional**
- Diseño normalizado
- Triggers para automatización
- Procedimientos almacenados
- Funciones personalizadas

### **3. Interfaz de Usuario Moderna**
- Diseño responsivo
- Componentes interactivos
- Experiencia de usuario optimizada

### **4. Seguridad Implementada**
- Autenticación robusta
- Autorización granular
- Protección contra ataques comunes

### **5. Automatización**
- Generación automática de datos
- Cálculos automáticos
- Alertas automáticas

---

## **ENTREGABLES CUMPLIDOS**

### **✅ Aplicación Web Completa**
- Sistema funcional y probado
- Interfaz responsiva
- Todas las funcionalidades implementadas

### **✅ Código Fuente Organizado**
- Estructura MVC clara
- Código bien documentado
- Estándares de codificación seguidos

### **✅ Base de Datos**
- Script SQL completo
- Triggers y procedimientos
- Datos de prueba incluidos

### **✅ Documentación**
- Manual de usuario
- Documentación técnica
- Guía de instalación

---

## **CONCLUSIÓN**

El proyecto **Sistema de Gestión de Taller Mecánico** representa una aplicación web completa y funcional que cumple con todos los requisitos especificados. La complejidad técnica implementada incluye:

- **Sistema de permisos avanzado**
- **Gestión de inventario automatizada**
- **Relaciones complejas entre entidades**
- **Interfaz de usuario moderna**
- **Seguridad robusta**
- **Reportes y análisis**

El nivel de complejidad es **ALTO**, demostrando competencias avanzadas en desarrollo web, bases de datos y gestión de proyectos software. 