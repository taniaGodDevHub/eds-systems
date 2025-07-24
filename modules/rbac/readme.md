# Установка модуля управления пользователями #

+  Разархивировать архив rbac.zip в папку modules/rbac
+ Добавить в config/web.php следующий код блок modules
```
'rbac' => [
            'class' => 'app\modules\rbac\Rbac',
        ],
```
+ Добавить в файлах config/web.php , config/console.php. Блок компоненты
```
'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
```
+ Выполнить миграцию
```
php yii migrate --migrationPath=@yii/rbac/migrations
```

+ Добавить файл AccessController.php в папку controllers
+ Для добавления пользователя выполнить следующий SQL запрос 
``` 
INSERT INTO user (username, auth_key, password_hash, password_reset_token, email, status, created_at, updated_at) VALUES
('admin', 'YdZ2jtC4aPc1SSWaMp_HzkkzuXKf0XLv', '$2y$13$7TIMNG/PCoUG4zpAkRHTDuS1b/0HbfCRfIkOm0A/I1.sy7R8ztpvy', NULL, 'admin@admin.admin', 10, 1688999065, 1688999065);
```
+ Этот запрос создаст пользователя admin, пароль: adminadmin и активирует его
+ Заменить файлы в папке views/site
+ Заменить SiteController.php в папке controllers
+ Добавить в папку models архив models.zip
+  В меню (для админа) добавить управление пользователями
```
 Yii::$app->user->can('admin') ?
                [
                'label' => 'Пользователи',
                'items'=>[
                    ['label' => 'Управление пользователями', 'url' => ['/users/users/index']],
                    ['label' => 'Роли и разрешения', 'url' => ['/rbac/auth-item/index']],
                    ['label' => 'Наследования', 'url' => ['/rbac/auth-item-child/index']],
                ]
            ] : '',
```