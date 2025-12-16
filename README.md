# Instalación y Configuración

## 1. Clonar el repositorio:

```Bash
git clone https://github.com/cjorgeluis122333/laravel-secuity-sample.git
cd laravel-secuity-sample
````

## 2. Instalar dependencias:

```Bash
composer install
```

## 3. Configurar el archivo .env:
### Ubuntu
```Bash
cp .env.example .env
php artisan key:generate
````
### Windows
```shell
php artisan key:generate
```
## 4. Crear la base de datos:
### Ubuntu
```Bash
 touch database/database.sqlite
````
### Windows
```shell
New-Item -Path database/database.sqlite -ItemType File
```

## 5. Ejecutar migraciones:
```Bash
php artisan migrate
```

## 6. Ejecutar seeders (opcional ):
```Bash
php artisan db:seed
```
## 7 Iniciar el servidor:

```Bash
 php artisan serve
```
La API estará disponible en http://localhost:8000/api

## Pruebas
Se incluye un script de prueba test_api.sh que prueba todos los endpoints:

```Bash
bash test_api.sh
```
