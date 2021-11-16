PAD
===

Local installation for developer with Docker
--------------------------------------------

Prerequisites: you should have Docker up and running. The development environment runs with `Docker compose` (heavily inspired by [Moodle Docker](https://github.com/moodlehq/moodle-docker/)). The PHP image used by the configuration has a custom `upload_max_filesize/post_max_size` of 100MB to enable plugin/backup/etc upload through Moodle web UI.

- copy `config-docker.php` to `config.php` (dedicated config to run Moodle with Docker environment variables)
- copy `.env.example` to `.env`
- edit `.env` to set custom environment variables for your local setup: you should at least set the `MOODLE_DOCKER_WWWROOT` to the project location, so that the container can load Moodle code.
- run `docker compose up -d` to launch the developer environment
- go to `http://localhost:8000` to check Moodle is ok and perform installation steps

Beware! Since we are using the local filesystem inside the Moodle container for development, you might run into issue related to file access. You may have to change file access for moodle to perform some modifications (e.g. when you upload a plugin or data...)

For example, allow anyone write access on theme folder to enable theme upload through Moodle Administration UI:
```
chmod a+w theme
```

To stop containers, run `docker-compose stop`. To restart, run `docker-compose stop`. If there is a configuration change, run `docker-compose up -d` so that Docker updates the environment.
To destroy the developer environment, run `docker-compose down`
