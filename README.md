# Sistema de vivero de cafe

Aplicacion Laravel 12 para gestionar un vivero de cafe: variedades, bandejas, lotes, plantas, mediciones, evaluaciones, eventos, fotos, cambios de estado y acceso publico por token.

## Arranque

1. Copia `.env.example` a `.env` y ajusta PostgreSQL.
2. Ejecuta `composer install`.
3. Ejecuta `php artisan migrate --seed`.
4. Ejecuta `php artisan serve`.

## Hosting en Hostinger

1. Sube el proyecto sin `node_modules`.
2. Copia `.env.hostinger.example` a `.env` y completa credenciales de MariaDB.
3. Ejecuta `php artisan key:generate` en el servidor si no tiene `APP_KEY`.
4. Ejecuta `php artisan migrate --seed`.
5. Si el hosting no permite `storage:link`, enlaza `storage/app/public` con `public/storage` manualmente.
6. Sirve el proyecto desde `public/`.

## Credenciales de prueba

- `admin@vivero.test` / `password`
- `operario@vivero.test` / `password`

## Modo de uso

- `Dashboard` para resumen general.
- `Plantas` para ver ficha y cronologia.
- `Admin` para CRUD de catalogos.
- `/publico/planta/{token}` para ficha publica.
