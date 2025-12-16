# API de Blog - Documentación

Esta es una API RESTful desarrollada con Laravel para un sistema de blog con autenticación segura, gestión de posts y comentarios.

## Características

- **Autenticación Segura**: Implementada con Laravel Sanctum
- **Modelos Relacionados**: Usuario, Post y Comentario con relaciones bien definidas
- **CRUD Completo**: Operaciones Create, Read, Update, Delete para Posts y Comentarios
- **Validación de Datos**: Validación robusta en todos los endpoints
- **Control de Acceso**: Solo los propietarios pueden editar o eliminar sus propios posts y comentarios
- **Manejo de Errores**: Respuestas JSON consistentes con códigos HTTP apropiados

## Estructura de la Base de Datos

### Tabla: users
- `id`: ID único del usuario
- `name`: Nombre del usuario
- `email`: Email único del usuario
- `password`: Contraseña hasheada
- `email_verified_at`: Fecha de verificación de email
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

### Tabla: posts
- `id`: ID único del post
- `user_id`: ID del usuario propietario
- `title`: Título del post
- `content`: Contenido del post
- `published_at`: Fecha de publicación
- `status`: Estado del post (draft, published, archived)
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

### Tabla: comments
- `id`: ID único del comentario
- `post_id`: ID del post al que pertenece
- `user_id`: ID del usuario que hizo el comentario
- `content`: Contenido del comentario
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

## Relaciones

- **Usuario → Posts**: Un usuario puede tener muchos posts (1:N)
- **Usuario → Comentarios**: Un usuario puede tener muchos comentarios (1:N)
- **Post → Comentarios**: Un post puede tener muchos comentarios (1:N)
- **Post → Usuario**: Un post pertenece a un usuario (N:1)
- **Comentario → Post**: Un comentario pertenece a un post (N:1)
- **Comentario → Usuario**: Un comentario pertenece a un usuario (N:1)

## Endpoints de Autenticación

### Registrar Usuario
```
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "password_confirmation": "SecurePassword123!"
}
```

**Respuesta (201 Created):**
```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-12-16T15:59:43.000000Z",
      "updated_at": "2025-12-16T15:59:43.000000Z"
    },
    "access_token": "1|token_string",
    "token_type": "Bearer"
  }
}
```

### Iniciar Sesión
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "SecurePassword123!"
}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Sesion iniciada exitosamente",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-12-16T15:59:43.000000Z",
      "updated_at": "2025-12-16T15:59:43.000000Z"
    },
    "access_token": "2|token_string",
    "token_type": "Bearer"
  }
}
```

### Obtener Usuario Autenticado
```
GET /api/auth/me
Authorization: Bearer {token}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-12-16T15:59:43.000000Z",
    "updated_at": "2025-12-16T15:59:43.000000Z"
  }
}
```

### Refrescar Token
```
POST /api/auth/refresh-token
Authorization: Bearer {token}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Token refrescado exitosamente",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-12-16T15:59:43.000000Z",
      "updated_at": "2025-12-16T15:59:43.000000Z"
    },
    "access_token": "3|new_token_string",
    "token_type": "Bearer"
  }
}
```

### Cerrar Sesión
```
POST /api/auth/logout
Authorization: Bearer {token}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Sesion cerrada exitosamente"
}
```

## Endpoints de Posts

### Obtener Todos los Posts
```
GET /api/posts
Authorization: Bearer {token}
```

**Parámetros de Query:**
- `page`: Número de página (default: 1)
- `per_page`: Posts por página (default: 15)

**Respuesta (200 OK):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "title": "Mi Primer Post",
        "content": "Contenido del post...",
        "published_at": "2025-12-16T15:59:43.000000Z",
        "status": "published",
        "created_at": "2025-12-16T15:59:43.000000Z",
        "updated_at": "2025-12-16T15:59:43.000000Z",
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "comments": []
      }
    ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

### Crear Post
```
POST /api/posts
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Mi Nuevo Post",
  "content": "Este es el contenido de mi nuevo post",
  "status": "published",
  "published_at": "2025-12-16"
}
```

**Respuesta (201 Created):**
```json
{
  "success": true,
  "message": "Post creado exitosamente",
  "data": {
    "id": 131,
    "user_id": 1,
    "title": "Mi Nuevo Post",
    "content": "Este es el contenido de mi nuevo post",
    "published_at": "2025-12-16T15:59:43.000000Z",
    "status": "published",
    "created_at": "2025-12-16T15:59:43.000000Z",
    "updated_at": "2025-12-16T15:59:43.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

### Obtener Post por ID
```
GET /api/posts/{id}
Authorization: Bearer {token}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "title": "Mi Primer Post",
    "content": "Contenido del post...",
    "published_at": "2025-12-16T15:59:43.000000Z",
    "status": "published",
    "created_at": "2025-12-16T15:59:43.000000Z",
    "updated_at": "2025-12-16T15:59:43.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "comments": [
      {
        "id": 1,
        "post_id": 1,
        "user_id": 2,
        "content": "Excelente post!",
        "created_at": "2025-12-16T15:59:43.000000Z",
        "updated_at": "2025-12-16T15:59:43.000000Z",
        "user": {
          "id": 2,
          "name": "Jane Doe",
          "email": "jane@example.com"
        }
      }
    ]
  }
}
```

### Actualizar Post
```
PUT /api/posts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Título Actualizado",
  "content": "Contenido actualizado",
  "status": "draft"
}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Post actualizado exitosamente",
  "data": {
    "id": 1,
    "user_id": 1,
    "title": "Título Actualizado",
    "content": "Contenido actualizado",
    "published_at": "2025-12-16T15:59:43.000000Z",
    "status": "draft",
    "created_at": "2025-12-16T15:59:43.000000Z",
    "updated_at": "2025-12-16T16:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

### Eliminar Post
```
DELETE /api/posts/{id}
Authorization: Bearer {token}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Post eliminado exitosamente"
}
```

## Endpoints de Comentarios

### Obtener Todos los Comentarios
```
GET /api/comments
Authorization: Bearer {token}
```

**Parámetros de Query:**
- `page`: Número de página (default: 1)
- `per_page`: Comentarios por página (default: 20)

**Respuesta (200 OK):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "post_id": 1,
        "user_id": 2,
        "content": "Excelente post!",
        "created_at": "2025-12-16T15:59:43.000000Z",
        "updated_at": "2025-12-16T15:59:43.000000Z",
        "user": {
          "id": 2,
          "name": "Jane Doe",
          "email": "jane@example.com"
        },
        "post": {
          "id": 1,
          "user_id": 1,
          "title": "Mi Primer Post",
          "content": "Contenido del post..."
        }
      }
    ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

### Crear Comentario
```
POST /api/comments
Authorization: Bearer {token}
Content-Type: application/json

{
  "post_id": 1,
  "content": "Este es un comentario excelente!"
}
```

**Respuesta (201 Created):**
```json
{
  "success": true,
  "message": "Comentario creado exitosamente",
  "data": {
    "id": 101,
    "post_id": 1,
    "user_id": 2,
    "content": "Este es un comentario excelente!",
    "created_at": "2025-12-16T15:59:44.000000Z",
    "updated_at": "2025-12-16T15:59:44.000000Z",
    "user": {
      "id": 2,
      "name": "Jane Doe",
      "email": "jane@example.com"
    },
    "post": {
      "id": 1,
      "user_id": 1,
      "title": "Mi Primer Post",
      "content": "Contenido del post..."
    }
  }
}
```

### Obtener Comentario por ID
```
GET /api/comments/{id}
Authorization: Bearer {token}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "post_id": 1,
    "user_id": 2,
    "content": "Excelente post!",
    "created_at": "2025-12-16T15:59:43.000000Z",
    "updated_at": "2025-12-16T15:59:43.000000Z",
    "user": {
      "id": 2,
      "name": "Jane Doe",
      "email": "jane@example.com"
    },
    "post": {
      "id": 1,
      "user_id": 1,
      "title": "Mi Primer Post",
      "content": "Contenido del post..."
    }
  }
}
```

### Actualizar Comentario
```
PUT /api/comments/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "Comentario actualizado"
}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Comentario actualizado exitosamente",
  "data": {
    "id": 1,
    "post_id": 1,
    "user_id": 2,
    "content": "Comentario actualizado",
    "created_at": "2025-12-16T15:59:43.000000Z",
    "updated_at": "2025-12-16T15:59:45.000000Z",
    "user": {
      "id": 2,
      "name": "Jane Doe",
      "email": "jane@example.com"
    },
    "post": {
      "id": 1,
      "user_id": 1,
      "title": "Mi Primer Post",
      "content": "Contenido del post..."
    }
  }
}
```

### Eliminar Comentario
```
DELETE /api/comments/{id}
Authorization: Bearer {token}
```

**Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Comentario eliminado exitosamente"
}
```

## Códigos de Error

| Código | Descripción |
|--------|-------------|
| 200 | OK - Solicitud exitosa |
| 201 | Created - Recurso creado exitosamente |
| 401 | Unauthorized - Credenciales inválidas o token expirado |
| 403 | Forbidden - No tienes permiso para acceder a este recurso |
| 404 | Not Found - El recurso no existe |
| 422 | Unprocessable Entity - Error de validación |
| 500 | Internal Server Error - Error del servidor |

## Requisitos de Seguridad

- **Contraseñas**: Deben tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números
- **Tokens**: Los tokens de Sanctum expiran después de un período de inactividad
- **CORS**: La API está configurada para aceptar solicitudes desde cualquier origen
- **Validación**: Todos los datos de entrada se validan antes de procesarse
- **Control de Acceso**: Solo los propietarios pueden editar o eliminar sus propios recursos

## Instalación y Configuración

1. Clonar el repositorio:
```bash
git clone https://github.com/cjorgeluis122333/laravel-secuity-sample.git
cd laravel-secuity-sample
```

2. Instalar dependencias:
```bash
composer install
```

3. Configurar el archivo .env:
```bash
cp .env.example .env
php artisan key:generate
```

4. Crear la base de datos:
```bash
touch database/database.sqlite
```

5. Ejecutar migraciones:
```bash
php artisan migrate
```

6. Ejecutar seeders (opcional):
```bash
php artisan db:seed
```

7. Iniciar el servidor:
```bash
php artisan serve
```

La API estará disponible en `http://localhost:8000/api`

## Pruebas

Se incluye un script de prueba `test_api.sh` que prueba todos los endpoints:

```bash
bash test_api.sh
```

## Licencia

Este proyecto está bajo la licencia MIT.
