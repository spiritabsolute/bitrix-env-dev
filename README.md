# Bitrix на Docker

Запуск Bitrix дистрибутива на Docker.
Используются оффициальные образы php, nginx, mysql, composer.
Учтена возможность линковки папки проекта на локальной машине и тестирования c phpunit.

## Зависимости
- Git
- Docker & Docker-Compose
```
cd /usr/local/src && wget -qO- https://get.docker.com/ | sh && \
curl -L "https://github.com/docker/compose/releases/download/1.18.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose && \
chmod +x /usr/local/bin/docker-compose && \
echo "alias dc='docker-compose'" >> ~/.bash_aliases && \
source ~/.bashrc
```

### Начало работы
- Склонируйте репозиторий
```
git clone https://github.com/spiritabsolute/bitrix-env-dev.git
```

- Выполните настройку окружения

Скопируйте файл `.env_template` в `.env`

```
cp -f .env_template .env
```
⚠️ Если у вас мак или windows, то удалите строчку /etc/localtime:/etc/localtime/:ro из docker-compose

По умолчанию используется php_72, эти настройки можно изменить в файле ```.env```. 
Скопируйте файл php_72/ssmtp_template.conf в php_72/ssmtp.conf и заполните свои данные.
Также нужно задать путь к директории с сайтом, репозиторием для линковки и параметры базы данных MySQL.
Если нужна линковка не нужна удалите из docker-composer линковку в ${REPOSITORY_FOR_LINKS}

```
PHP_VERSION=php_72                         # Версия php 
MYSQL_DATABASE=bitrix                      # Имя базы данных
MYSQL_USER=bitrix                          # Пользователь базы данных
MYSQL_PASSWORD=bitrix                      # Пароль для доступа к базе данных
MYSQL_ROOT_PASSWORD=bitrix                 # Пароль для пользователя root от базы данных
SERVER_IDE=docker                          # Имя сервера, которое будет подставлено в константу PHP_IDE_CONFIG
INTERFACE=0.0.0.0                          # На данный интерфейс будут проксироваться порты
SITE_PATH=/var/www/bitrix                  # Путь к директории Вашего сайта
REPOSITORY_FOR_LINKS=/var/repository/base/ # Путь к директории с репозиторием

```

- Выполните в директории с файлом docker-compose.ymp команду
```
docker-compose up --build
```
Чтобы проверить, что все сервисы запустились посмотрите список процессов ```docker ps```.  
Посмотрите все прослушиваемые порты, должны быть 80, 11211, 9000 ```netstat -plnt```.  
Откройте IP машины в браузере.

## Примечание
- В настройках подключения требуется указывать имя сервиса, например для подключения к mysql нужно указывать "mysql", а не "localhost".
- Для загрузки резервной копии в контейнер используйте команду: ```cat /var/www/bitrix/backup.sql | docker exec -i mysql /usr/bin/mysql -u root -p bitrix bitrix```
- Для запуска тестов допишите путь site_path в phpunit.xml и настройте ide на работу с /phpunit.xml и /.bootstrap.php
