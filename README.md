# file-manager
 Менеджер файлов, реализованный на Laravel + Vue

## Возможности
- Создание файлов
- Создание папок
- Редактирование файлов (с prism редактором)
- Копирование файлов
- Удаление файлов
- Переименовывание файлов
- Загрузка файлов
- Просмотр медиа
- Zip / Unzip

## Зависимости

- Laravel **^6.0** or **^7.0**
- PHP **7.2**

## Установка

Через composer:

```bash
composer require srustamov/laravel-file-manager
```

```bash
php artisan vendor:publish --provider="Srustamov\FileManager\FileManagerServiceProvider" --tag="config"
```
```bash
php artisan vendor:publish --provider="Srustamov\FileManager\FileManagerServiceProvider" --tag="public" --force
```
php artisan vendor:publish --provider="Srustamov\FileManager\FileManagerServiceProvider" --tag="public" --force
