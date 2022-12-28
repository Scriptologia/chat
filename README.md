## Чат на websocket
##### Требования:
 1. таблица и модель __App\Models\User__
 2. установленная зависимость __Laravel Websockets__ :
 
     > https://beyondco.de/docs/laravel-websockets/getting-started/installation
 ##### Используются зависимости js в package.json :
    "laravel-echo": "^1.11.7"
    "pusher-js": "^4.4.0"
   
##### Установка
    composer require scriptologia/chat
    php artisan vendor:publish --provider=Scriptologia\Chat\Providers\ChatServiceProvider
##### Обязательно опубликуйте стили и скрипты
    php artisan vendor:publish --tag=chat-public --force
##### При необходимости опубликуйте файл конфигурации chat.php
    php artisan vendor:publish --tag=chat-config --force
##### При необходимости опубликуйте файл view
    php artisan vendor:publish --tag=chat-views --force


