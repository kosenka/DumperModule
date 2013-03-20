DumperModule - Модуль для Yii Framework для бэкапа/восстановления базы данных
=======

## Установка

* Скачать ([zip](https://github.com/kosenka/DumperModule/zipball/master), [tar.gz](https://github.com/kosenka/DumperModule/tarball/master)).

* Распаковать архив в папку `application.modules.DumperModule` . Должно получиться следующее:

```
protected/
├── components/
├── controllers/
├── ... application directories
└── modules/
    ├── DumperModule/
    │   ├── views/
    │   └── ... другие файлы модуля DumperModule
    └── ... другие модули
```

## ССылки

* [Extension project page](https://github.com/kosenka/DumperModule)
* [Russian community discussion thread](http://yiiframework.ru/forum/)

## Использование
В конфиге приложения прописать так:

```php
  'modules'=>array(
                        'dumper'=>array(
                                              'class' => 'application.modules.dumper.DumperModule',
                                              'ips'=>array( //с каких IP-адресов разрешен доступ
                                                           'XX.XX.XX.XX', 
                                                           'XXX.XXX.XXX.XXX',
                                                           ),
                                             ),
        ),
```

