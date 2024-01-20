Обзор

Эта папка содержит сценарии и настройки для запуска радиосервера mi в контейнерах Docker. Скрипты можно использовать для быстрого предоставления собственного радиосервера в облаке, локально или у других хостинг-провайдеров, поддерживающих доккеризацию.

Как использовать

На сервере

Установите докер [https://docs.docker.com/install/linux/docker-ce .](https://docs.docker.com/engine/install/debian/)

Установите docker compose [https://docs.docker.com/compose/install/#install-compose .](https://docs.docker.com/compose/install/linux/#install-using-the-repository)

Клонируйте этот репозиторий с помощью 
```
git clone https://github.com/BarkanovSergey/miwifiradio
```

Перейдите в папку
```
cd miwifiradio/docker/
```

Настройте конфигурацию в соответствии с вашей средой в файле docker/.env 
```
nano .env
```

Разверните докер-контейнеры:
```
docker compose up -d
```
Перейдите по адресу http://<ваш хост>/admin.php и создайте учетную запись администратора.

Добавьте радиостанцию ​​после входа в систему под своей учетной записью администратора.


На мобильном устройстве
Вам необходимо перенастроить свое мобильное устройство, чтобы использовать новый сервер для api.ximalaya.com.

