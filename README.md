### Setup

* Clone this repository and de-gitify it (`rm -r .git`)
* Place a Symfony app in /symfony, either by `symfony new app` or by importing an existing project
* Run setup-file-permissions.sh to make Symfony app's `var` directory writeable by Apache using setfacl (if running under an OS that doesn't support setfacl, you may need to do this an alternative way. See https://symfony.com/doc/current/setup/file_permissions.html)
* Change your `parameters.yml` file to match the database settings, which you can do with environment variables (from .env file):
    database_name: "%env(MYSQL_DATABASE)%"
    database_user: "%env(MYSQL_USER)%"
    database_password: "%env(MYSQL_PASSWORD)%"
* `docker-compose up -d`

### Includes

* Apache/PHP7.1
* MariaDB
* Composer
* Elasticsearch
* Redis
* ELK stack for logs (Elasticsearch, Logstash, Kibana)

Expects a symfony app within /symfony directory

### Running commands

Symfony commands can be run using the symfony container, e.g.

`docker-compose exec symfony bin/console debug:router`

Setting up an alias within your shell for `docker-compose exec symfony php bin/console` is a good idea.

### Using Composer

Composer commands can be run within the composer service container:

`docker-compose run composer info`

Setting up an alias within your shell for `docker-compose run composer` is a good idea.

### Using services

Elasticsearch is available at elasticsearch:9200 from any other container.

Redis is available at redis:5379 from any other container.

ELK stack is available at localhost:8080

### Hosts file

127.0.0.1 symfony.dev