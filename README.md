Docker
======

1. Подготовительные шаги:
    * Install [docker](https://docs.docker.com/engine/installation/)

1. Для доступа в shell контейнера:

    ```shell
    docker-compose exec {service} /bin/bash
    ```

> Логи хранятся вне контейнера, в директории `./logs/`

---

#### Запуск через `docker run` (актуально для MacOS)
 
```shell
cd project_path
docker build --no-cache=true -t str-worker .docker
docker run -d -p 8000:80 -v $(pwd):/var/www/project --name str-worker str-worker
```

#### Подтягиваем необходимые зависимости

```shell
composer update
```

#### Запускаем Unit-тесты

```shell
phpunit tests
```
