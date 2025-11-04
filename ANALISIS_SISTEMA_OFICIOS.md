# Sistema de Gestión de Respuestas a Oficios - DIPAMPCO

## 1. Descripción General del Sistema

Sistema web para automatizar la gestión y respuesta de oficios (solicitudes de información) provenientes de diferentes entes de investigación policial en Honduras. El sistema permite registrar solicitudes, gestionar respuestas y mantener un control de correlativo de oficios.

---

## 2. Flujo del Proceso

### 2.1 Proceso Actual Identificado

1. **Recepción de Solicitud**: Llega un oficio de una unidad policial solicitando información sobre personas
2. **Registro de Solicitud**: Se registra el oficio entrante con sus datos
3. **Consulta de Base de Datos**: Se verifica si las personas están registradas en el sistema DIPAMPCO
4. **Generación de Respuesta**: Se crea un oficio de respuesta con formato estándar
5. **Firma y Envío**: El analista y jefe regional firman el documento
6. **Control de Correlativo**: Cada respuesta tiene un número único correlativo (Ej: RE-1432-2025)

---

## 3. Estructura de Base de Datos

### 3.1 Tablas Principales

#### **instituciones**
Almacena las instituciones/entes que solicitan información
```
- id (PK)
- nombre (DPI, PMOP, ATIC, etc.)
- nombre_completo
- activo
- created_at, updated_at
```

#### **unidades**
Unidades específicas dentro de cada institución
```
- id (PK)
- institucion_id (FK)
- nombre (URID, Delitos Contra la Vida, etc.)
- ciudad
- departamento
- activo
- created_at, updated_at
```

#### **cargos**
Catálogo de cargos policiales
```
- id (PK)
- nombre (Agente de Investigación, Sub Comisionado, etc.)
- nivel_jerarquico
- activo
- created_at, updated_at
```

#### **agentes**
Personal policial que solicita u firma oficios
```
- id (PK)
- nombres
- apellidos
- cargo_id (FK)
- unidad_id (FK)
- tipo (solicitante, firmante)
- activo
- created_at, updated_at
```

#### **delitos**
Catálogo de tipos de delitos
```
- id (PK)
- nombre (Tráfico de Drogas, Homicidio, etc.)
- descripcion
- activo
- created_at, updated_at
```

#### **solicitudes_oficios**
Oficios entrantes (solicitudes)
```
- id (PK)
- numero_oficio_entrante (1044-2025)
- fecha_recepcion
- institucion_id (FK)
- unidad_id (FK)
- agente_solicitante_id (FK)
- delito_id (FK)
- ofendido (La salud pública del Estado de Honduras)
- observaciones
- estado (pendiente, en_proceso, respondida)
- usuario_registro_id (FK -> users)
- created_at, updated_at
```

#### **personas_solicitadas**
Personas sobre las que se solicita información
```
- id (PK)
- solicitud_oficio_id (FK)
- nombres
- apellidos
- dni (0501-1995-04245)
- fecha_nacimiento (opcional)
- created_at, updated_at
```

#### **personas_registradas**
Base de datos de personas registradas en DIPAMPCO
```
- id (PK)
- nombres
- apellidos
- dni (UNIQUE)
- fecha_nacimiento
- grupo_delictivo
- estructura_criminal
- observaciones
- foto (opcional)
- activo
- created_at, updated_at
```

#### **respuestas_oficios**
Oficios de respuesta generados
```
- id (PK)
- solicitud_oficio_id (FK)
- numero_oficio_respuesta (RE-1432-2025)
- correlativo (1432)
- año (2025)
- fecha_respuesta
- analista_id (FK -> users)
- jefe_regional_id (FK -> users)
- contenido_respuesta (TEXT)
- estado (borrador, firmada, enviada)
- created_at, updated_at
```

#### **resultados_consulta**
Resultados de consulta por persona solicitada
```
- id (PK)
- respuesta_oficio_id (FK)
- persona_solicitada_id (FK)
- persona_registrada_id (FK - nullable)
- encontrada (boolean)
- detalles (JSON - info adicional si está registrada)
- created_at, updated_at
```

#### **configuracion_sistema**
Configuraciones generales
```
- id (PK)
- clave (UNIQUE - correlativo_actual, año_actual, etc.)
- valor
- descripcion
- created_at, updated_at
```

#### **users** (ya existe en Laravel)
Usuarios del sistema (analistas, jefes regionales)
```
- id (PK)
- name
- email
- password
- rol (admin, analista, jefe_regional, consulta)
- activo
- remember_token
- created_at, updated_at
```

---

## 4. Relaciones entre Tablas

### Relaciones Principales:

1. **instituciones** → **unidades** (1:N)
2. **unidades** → **agentes** (1:N)
3. **cargos** → **agentes** (1:N)
4. **instituciones** → **solicitudes_oficios** (1:N)
5. **unidades** → **solicitudes_oficios** (1:N)
6. **agentes** → **solicitudes_oficios** (1:N)
7. **delitos** → **solicitudes_oficios** (1:N)
8. **solicitudes_oficios** → **personas_solicitadas** (1:N)
9. **solicitudes_oficios** → **respuestas_oficios** (1:1)
10. **respuestas_oficios** → **resultados_consulta** (1:N)
11. **personas_solicitadas** → **resultados_consulta** (1:1)
12. **personas_registradas** → **resultados_consulta** (1:N)
13. **users** → **solicitudes_oficios** (1:N - usuario que registró)
14. **users** → **respuestas_oficios** (1:N - analista)
15. **users** → **respuestas_oficios** (1:N - jefe regional)

---

## 5. Módulos del Sistema

### 5.1 Módulo de Catálogos (Administración)
- Gestión de Instituciones
- Gestión de Unidades
- Gestión de Cargos
- Gestión de Agentes
- Gestión de Delitos
- Gestión de Usuarios

### 5.2 Módulo de Personas Registradas
- Registro de personas en base de datos DIPAMPCO
- Búsqueda y consulta
- Actualización de información
- Historial de consultas

### 5.3 Módulo de Solicitudes
- Registro de oficio entrante
- Registro de personas solicitadas
- Asignación de analista
- Estados de solicitud

### 5.4 Módulo de Respuestas
- Generación de respuesta
- Consulta automática de personas
- Generación de número correlativo
- Previsualización de oficio
- Firma digital/registro de firmas
- Generación de PDF

### 5.5 Módulo de Reportes
- Solicitudes por período
- Respuestas generadas
- Estadísticas por institución
- Estadísticas por delito
- Tiempos de respuesta

---

## 6. Páginas/Vistas del Sistema

### 6.1 Autenticación
- `/login` - Login
- `/register` - Registro (solo admin)
- `/forgot-password` - Recuperar contraseña

### 6.2 Dashboard
- `/dashboard` - Vista principal con estadísticas

### 6.3 Catálogos
- `/instituciones` - Lista de instituciones
- `/instituciones/crear` - Crear institución
- `/instituciones/{id}/editar` - Editar institución
- `/unidades` - Lista de unidades
- `/unidades/crear` - Crear unidad
- `/unidades/{id}/editar` - Editar unidad
- `/cargos` - Lista de cargos
- `/agentes` - Lista de agentes
- `/agentes/crear` - Crear agente
- `/agentes/{id}/editar` - Editar agente
- `/delitos` - Lista de delitos

### 6.4 Personas Registradas
- `/personas-registradas` - Lista de personas en BD DIPAMPCO
- `/personas-registradas/crear` - Registrar persona
- `/personas-registradas/{id}` - Ver detalle
- `/personas-registradas/{id}/editar` - Editar persona
- `/personas-registradas/buscar` - Búsqueda avanzada

### 6.5 Solicitudes
- `/solicitudes` - Lista de solicitudes
- `/solicitudes/crear` - Registrar nueva solicitud
- `/solicitudes/{id}` - Ver detalle de solicitud
- `/solicitudes/{id}/editar` - Editar solicitud
- `/solicitudes/{id}/responder` - Generar respuesta

### 6.6 Respuestas
- `/respuestas` - Lista de respuestas
- `/respuestas/{id}` - Ver detalle de respuesta
- `/respuestas/{id}/previsualizar` - Vista previa del oficio
- `/respuestas/{id}/pdf` - Descargar PDF
- `/respuestas/{id}/editar` - Editar respuesta (si está en borrador)

### 6.7 Reportes
- `/reportes/solicitudes` - Reporte de solicitudes
- `/reportes/respuestas` - Reporte de respuestas
- `/reportes/estadisticas` - Estadísticas generales
- `/reportes/instituciones` - Por institución
- `/reportes/tiempos` - Tiempos de respuesta

### 6.8 Configuración
- `/configuracion/sistema` - Configuración general
- `/configuracion/usuarios` - Gestión de usuarios
- `/configuracion/permisos` - Permisos y roles

---

## 7. Funcionalidades Clave

### 7.1 Generación Automática de Correlativo
```
Formato: RE-{correlativo}-{año}
Ejemplo: RE-1432-2025

El sistema debe:
- Incrementar automáticamente el correlativo
- Reiniciar el correlativo al cambiar de año
- Prevenir duplicados con transacciones
```

### 7.2 Consulta Automática de Personas
Al generar una respuesta:
1. Tomar las personas de la solicitud
2. Buscar en la tabla `personas_registradas` por DNI
3. Marcar si fueron encontradas o no
4. Generar tabla de resultados automáticamente

### 7.3 Plantilla de Respuesta
El sistema debe generar automáticamente el formato estándar:
```
República de Honduras
Secretaría de Seguridad
Dirección General Policía Nacional
Dirección Policial Antimaras y Pandillas Contra el Crimen Organizado
San Pedro Sula, Cortés
{fecha}

OFICIO DIPAMPCO N° {numero_correlativo}

Señor
{nombre_agente}
{cargo}
{institucion}
{unidad}
Su oficina.

Por medio de la presente...

[Tabla de personas consultadas]

Referente a lo solicitado se informa...

[Firmas]
```

### 7.4 Búsqueda y Filtros
- Búsqueda de solicitudes por número, fecha, institución
- Filtros por estado, delito, unidad
- Búsqueda de personas por DNI, nombre
- Exportación de resultados

---

## 8. Stack Tecnológico Propuesto

### Backend
- **Laravel 12** (PHP 8.4)
- **MySQL** para base de datos
- **Laravel Fortify** para autenticación

### Frontend
- **Inertia.js v2** (Vue 3)
- **Tailwind CSS v4** para estilos
- **Vue 3 Composition API**

### Generación de Documentos
- **Laravel DomPDF** o **Snappy (wkhtmltopdf)** para PDFs
- Plantillas Blade para estructura de oficios

### Testing
- **Pest PHP v4** para testing

---

## 9. Permisos y Roles

### Roles del Sistema:

1. **Administrador**
   - Acceso total al sistema
   - Gestión de usuarios y catálogos
   - Configuración del sistema

2. **Analista**
   - Registrar solicitudes
   - Generar respuestas
   - Consultar personas registradas
   - Registrar personas en BD

3. **Jefe Regional**
   - Aprobar/firmar respuestas
   - Ver reportes
   - Consultas

4. **Consulta**
   - Solo lectura
   - Ver solicitudes y respuestas
   - No puede editar

---

## 10. Fases de Implementación

### Fase 1: Base y Autenticación (Semana 1)
- Instalación y configuración de Laravel
- Sistema de autenticación con Fortify
- Migraciones de base de datos
- Seeders iniciales

### Fase 2: Catálogos (Semana 2)
- CRUD de instituciones
- CRUD de unidades
- CRUD de cargos
- CRUD de agentes
- CRUD de delitos
- CRUD de usuarios

### Fase 3: Personas Registradas (Semana 3)
- CRUD de personas registradas
- Búsqueda avanzada
- Importación masiva (opcional)

### Fase 4: Solicitudes (Semana 4)
- Registro de solicitudes
- Gestión de personas solicitadas
- Vista de detalle
- Estados de solicitud

### Fase 5: Respuestas (Semana 5)
- Generación de respuestas
- Sistema de correlativo
- Consulta automática
- Previsualización

### Fase 6: Documentos y Reportes (Semana 6)
- Generación de PDFs
- Reportes estadísticos
- Exportaciones
- Dashboard con gráficas

### Fase 7: Testing y Refinamiento (Semana 7)
- Tests unitarios
- Tests de integración
- Correcciones
- Documentación

---

## 11. Diseño de Interfaz (UI/UX)

### 11.1 Layout Principal
- **Header**: Logo DIPAMPCO, nombre de usuario, logout
- **Sidebar**: Menú de navegación con iconos
- **Contenido**: Área principal de trabajo
- **Footer**: Copyright y versión

### 11.2 Esquema de Colores (Sugerido)
- **Primario**: Azul institucional (#1e3a8a)
- **Secundario**: Gris oscuro (#374151)
- **Éxito**: Verde (#10b981)
- **Advertencia**: Amarillo (#f59e0b)
- **Error**: Rojo (#ef4444)
- **Fondo**: Blanco/Gris claro (#f9fafb)

### 11.3 Componentes Reutilizables
- Tablas con paginación y ordenamiento
- Formularios con validación
- Modales para confirmaciones
- Alertas y notificaciones
- Botones de acción
- Cards para información
- Badges para estados

### 11.4 Responsividad
- Desktop first (uso principal en oficinas)
- Adaptable a tablets
- Vista móvil básica para consultas

---

## 12. Seguridad

### 12.1 Medidas de Seguridad
- Autenticación con Laravel Fortify
- Validación de datos en backend
- Protección CSRF
- Sanitización de entradas
- Encriptación de passwords
- Control de acceso por roles
- Logs de auditoría

### 12.2 Auditoría
Registrar en una tabla `audit_logs`:
- Usuario que realizó la acción
- Acción realizada
- Tabla afectada
- Datos anteriores y nuevos
- Fecha y hora
- IP del usuario

---

## 13. Consideraciones Especiales

### 13.1 Backup
- Respaldos automáticos diarios de BD
- Respaldos de PDFs generados
- Retención de respaldos por 6 meses

### 13.2 Performance
- Índices en columnas de búsqueda frecuente (DNI, número_oficio)
- Cache de catálogos
- Paginación en listados
- Eager loading en relaciones

### 13.3 Validaciones Importantes
- DNI debe tener formato válido hondureño (####-####-#####)
- Números de oficio únicos
- Correlativo único por año
- Fechas coherentes (respuesta >= solicitud)

---

## 14. Métricas y KPIs

### Indicadores a Medir:
- Número de solicitudes por mes
- Tiempo promedio de respuesta
- Solicitudes por institución
- Personas consultadas vs encontradas
- Usuarios más activos
- Delitos más consultados

---

## 15. Próximos Pasos

1. **Revisión y aprobación** de este documento
2. **Creación de mockups** de las pantallas principales
3. **Configuración del entorno** de desarrollo
4. **Inicio de implementación** por fases
5. **Reuniones de seguimiento** semanales

---

## Contacto y Documentación

- **Repositorio**: (pendiente)
- **Documentación Técnica**: (pendiente)
- **Manual de Usuario**: (pendiente)

---

**Fecha de creación**: 4 de noviembre de 2025
**Versión**: 1.0
**Estado**: Propuesta Inicial
