# 🛠 Guía de Instalación - Proyecto INFORMATICA

## 1. Clonar Repositorio
```bash
git clone git@github.com:WuizZamora/Informatica.git
```

## 2. Renombrar Carpeta
```bash
mv Informatica INFORMATICA
```
o manualmente lo puedes hacer

## 3. Instalar Dependencias
```bash
cd INFORMATICA
composer install
```

## 4. Configurar archivo `config.php`
**Ruta:** `app/config/config.php`

```php
<?php
// Nombre del sitio
define('SITE_NAME', 'INFORMATICA | INVEA');

// Rutas base (¡Actualizar IP!)
define('BASE_URL', 'http://localhost/INFORMATICA/');
define('BASE_PATH', __DIR__ . '/../');
define('ASSETS_URL', BASE_URL . 'public/');

// Modo desarrollo
define('DEBUG_MODE', true);

// Configuración de errores
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Roles de usuario
define('ADMIN_ROLE', 1);
define('USER_ROLE', 2);

// Paginación
define('RESULTS_PER_PAGE', 20);
?>
```

## 5. Configurar archivo `.env`
**Ruta:** `.env` (en raíz del proyecto)

```env
# Configuración Base de Datos
DB_HOST=     # Ej: localhost o IP del servidor
DB_USER=     # Usuario de la base de datos
DB_PASS=     # Contraseña del usuario
DB_NAME=     # Nombre de la base de datos
```

## ✅ Checklist de Verificación
- Repositorio clonado correctamente
- Carpeta renombrada a `INFORMATICA` **en mayúsculas**.
- Dependencias instaladas con `composer install`
- Archivo `config.php` creado con la IP correcta
- Archivo `.env` configurado con credenciales válidas

## ⚠️ Notas Importantes
1. **Seguridad:**  
   - Nunca subir el archivo `.env` al repositorio (ya esta excluido el .gitignore)

2. **Requisitos Servidor:**  
   - PHP 7.4 o superior
   - Extensiones PHP: MySQLi, OpenSSL, Mbstring

3. **Permisos:**  
   ```bash
   chmod -R 755 storage/
   chmod -R 755 public/uploads/
   ```
