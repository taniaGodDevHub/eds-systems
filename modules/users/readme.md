# Установка модуля управления пользователями #

+  Разархивировать архив users.zip в папку modules/users
+ Добавить в config/web.php следующий код в раздел modules
```
'users' => [
            'class' => 'app\modules\users\Users',
        ],
```
+ Выполнить миграцию
```
yii migrate --migrationPath=@app/modules/users/migrations
```
+ путь ...r=users/users