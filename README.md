<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Controlador de Almacenamiento Seguro (PHP / JS)

**Autor:** Angel Peñaranda  
**Stack:** Laravel 12 (PHP 8.3) + Vanilla JavaScript + MySQL  
**Entorno:** Laragon (localhost)

---

## Descripción general

Este proyecto implementa un sistema de gestión de archivos seguro, con control de cuotas, grupos, roles y validaciones diseñadas para garantizar el uso responsable del almacenamiento.

Los usuarios pueden subir, descargar y eliminar sus archivos. El administrador puede gestionar grupos, asignar cuotas y definir extensiones prohibidas desde un panel central.

---

## Tecnologías y herramientas

-   Backend: Laravel 12 (PHP 8.3)
-   Frontend: JavaScript (ES6+)
-   Base de datos: MySQL
-   UI / Estilo: Bootstrap 5.3 + toasts personalizados
-   Servidor local: Laragon
-   Autenticación: Laravel Breeze (personalizado)

---

## Estructura general

```
app/
 ├── Http/
 │   ├── Controllers/
 │   │   ├── Admin/
 │   │   │   ├── GroupController.php
 │   │   │   ├── UserController.php
 │   │   │   └── SettingsController.php
 │   │   ├── FileController.php
 │   │   └── Controller.php
 │
 ├── Models/
 │   ├── User.php
 │   ├── Group.php
 │   ├── File.php
 │   └── Setting.php
 │
 └── Services/
     ├── FileValidationService.php
     └── StorageQuotaService.php
```

---

## Roles del sistema

| Rol           | Permisos                                                                                             |
| ------------- | ---------------------------------------------------------------------------------------------------- |
| Administrador | Crear, editar y eliminar grupos. Asignar usuarios a grupos. Definir cuotas y extensiones prohibidas. |
| Usuario       | Iniciar sesión y subir archivos dentro de su límite de cuota. Ver y eliminar sus archivos.           |

---

## Funcionalidades implementadas

### 1. Sistema de roles y grupos

-   CRUD completo para grupos.
-   Asignación de usuarios a grupos.
-   Cuotas configurables por grupo y usuario.

### 2. Panel de usuario

-   Subida de archivos mediante AJAX.
-   Listado con nombre, tamaño y fecha.
-   Eliminación y descarga seguras.
-   Indicadores de espacio usado y cuota disponible.

### 3. Lógica de subida de archivos

-   Validaciones completas en backend (PHP).
-   Cálculo de espacio usado y verificación de cuota antes de subir.
-   Validación por extensión (según configuración).
-   Análisis automático de archivos `.zip` (detecta archivos internos prohibidos).
-   Manejo visual de errores con toasts en el frontend.

### 4. Configuración global

-   Límite de cuota global editable (por defecto: 10 MB).
-   Gestión de extensiones prohibidas (separadas por coma).
-   Sección de configuración integrada al panel del administrador.

---

## Decisiones de diseño

-   **Separación de responsabilidades:**  
    La lógica de negocio se concentra en `app/Services`, manteniendo los controladores simples.

-   **Validación centralizada:**  
    Las reglas de seguridad y almacenamiento se manejan desde `FileValidationService` y `StorageQuotaService`.

-   **UX sin recargas:**  
    Los formularios de subida, edición y eliminación funcionan con JavaScript moderno (`fetch` / `XMLHttpRequest`) y toasts.

-   **Diseño mantenible:**  
    La arquitectura es modular y permite añadir nuevas validaciones sin romper las existentes.

---

## Instalación y configuración

1. **Clonar el repositorio**

    ```bash
    git clone https://github.com/angelfarid1998/storage-controller.git
    cd storage-controller
    ```

2. **Instalar dependencias**

    ```bash
    composer install
    npm install && npm run build
    ```

3. **Configurar entorno**
   Copiar `.env.example` a `.env`:

    ```bash
    cp .env.example .env
    ```

    Editar las variables de conexión a la base de datos:

    ```
    DB_DATABASE=storage_controller
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4. **Ejecutar migraciones y seeders**

    ```bash
    php artisan migrate --seed
    ```

5. **Iniciar el servidor**
    ```bash
    php artisan serve
    ```
    Acceder a `http://127.0.0.1:8000`

---

## Credenciales de prueba

| Rol           | Email          | Contraseña |
| ------------- | -------------- | ---------- |
| Administrador | admin@test.com | admin123   |
| Usuario       | user@test.com  | user123    |
| Test          | test@test.com  | test123    |

---

## Pruebas clave

| Prueba                                   | Resultado esperado                     |
| ---------------------------------------- | -------------------------------------- |
| Subida dentro de la cuota                | Permitido                              |
| Subida que excede la cuota               | Bloqueado con mensaje “Cuota excedida” |
| Subida de `.exe` o `.php`                | Bloqueado                              |
| Subida de `.zip` con archivos prohibidos | Bloqueado con detalle del archivo      |
| Subida de `.zip` válido                  | Permitido                              |

---

## Notas finales

-   Código 100% PHP OOP y JavaScript moderno.
-   Cumple con los requerimientos funcionales y no funcionales del documento de prueba.
-   Estructura clara, sin dependencias innecesarias.
-   Código legible y mantenible.
