Docker
======

1. Подготовительные шаги:
    * Install [docker](https://docs.docker.com/engine/installation/)

1. Для доступа в shell контейнера:

    ```shell
    docker-compose exec {service} /bin/bash
    ```

> Логи хранятся вне контейнера, в директории `./logs/`

> После запуска контейнера автоматически запускается наблюдатель очереди StrListener::run

> Запросы приходят в web/index.php, которые ставят в очередь задания и ожидают синхронного ответа из метода StrRequest::getResponse

---

#### Запуск через `docker run` (актуально для MacOS)
 
```shell
cd project_path
docker build --no-cache=true -t str-worker .docker
docker run -d -p 8000:80 -p 5672:5672 -v $(pwd):/var/www/project --name str-worker str-worker
```

#### Подтягиваем необходимые зависимости

```shell
composer update
```

#### Запускаем Unit-тесты

```shell
phpunit tests
```

#### Смотрим кол-во воркеров и увеличиваем если нужно

```shell
ps a | grep -e 'service\.php$' # колько уже запущено
php ${PR_ROOT}/service.php & # отвязываемся от процесса и получем pid для дальнейшего kill
```

#### Пример запроса

```shell
curl -d="{\"job\":{\"text\":\"some text\", \"methods\":[\"removeSpaces\"]}}" localhost:8000
# вернет {"text":"sometext"}
```