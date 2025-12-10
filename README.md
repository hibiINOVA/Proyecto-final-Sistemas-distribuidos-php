# Catálogo de Videojuegos - Backend (PHP)

Este proyecto es el servicio backend (API REST) construido en PHP para gestionar un catálogo de videojuegos. La persistencia de datos se maneja con **PostgreSQL**, ejecutado a través de un contenedor Docker.

## Inicio Rápido

Sigue estos pasos para levantar la base de datos y el servidor de la API en tu entorno local.

### 1\. Requisitos Previos

  * **PHP:** Debes tener PHP instalado (versión 8.x o superior recomendada).
  * **Docker:** Necesario para levantar la base de datos PostgreSQL.

### 2\. Configuración de la Base de Datos (PostgreSQL con Docker)

La aplicación requiere una instancia de PostgreSQL. Usaremos Docker para esto.

**A. Crear y Ejecutar el Contenedor PostgreSQL**

Ejecuta el siguiente comando para levantar el contenedor de la base de datos:

```bash
docker run -d \
    --name postgres_servicePhp \
    -e POSTGRES_DB=servicePhp \
    -e POSTGRES_USER=root \
    -e POSTGRES_PASSWORD=12345 \
    -p 5433:5432 \
    postgres:16
```

| Parámetro | Valor | Descripción |
| :--- | :--- | :--- |
| `--name` | `postgres_servicePhp` | Nombre del contenedor. |
| `-e POSTGRES_DB` | `servicePhp` | Nombre de la base de datos. |
| `-e POSTGRES_USER` | `root` | Usuario de la base de datos. |
| `-e POSTGRES_PASSWORD` | `12345` | Contraseña del usuario. |
| `-p 5433:5432` | | Mapea el puerto local `5433` al puerto interno de Docker `5432`. |
| `-d` | `postgres:16` | Versión de PostgreSQL a usar. |

### 3\. Script SQL para Crear la Tabla `videojuegos`

Una vez que el contenedor PostgreSQL esté corriendo, debes ejecutar el siguiente script SQL (DDL) para crear la tabla `videojuegos` en la base de datos `servicePhp`.

```sql
CREATE TABLE videojuegos (
    -- Clave primaria, CHAR(36) para almacenar UUIDs
    id CHAR(36) PRIMARY KEY,
    
    -- Detalles del Videojuego
    titulo VARCHAR(150) NOT NULL,
    desarrollador VARCHAR(100),
    plataforma VARCHAR(50),
    genero VARCHAR(50),
    
    -- Atributos numéricos
    año_lanzamiento INTEGER,
    precio DECIMAL(8,2),
    calificacion DECIMAL(2,1),
    
    -- Metadatos
    modo_juego VARCHAR(30),
    clasificacion VARCHAR(10),
    
    -- Referencia al usuario que lo registró
    usuario_registro CHAR(36) NOT NULL,
    
    -- Fechas de control
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4\. Ejecución del Servidor PHP

Navega al directorio donde se encuentra tu archivo `Public/Index.php` y ejecuta el servidor de desarrollo de PHP:

```bash
php -S localhost:8000 Public/Index.php
```

### Acceso a la API

El servidor API estará disponible en la dirección:

```
http://localhost:8000/
```
