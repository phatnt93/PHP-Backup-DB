# Backup DB

Backup DB (Mariadb)

## Technologies

- PHP 8.3

## Deploy

```bash
git clone
composer install
# Edit config
cp config.ini.ex config.ini
```

## Usage

```bash
php index.php >> logs/run.log
```

Crontab every day

```text
0 0 * * * php index.php >> logs/run.log
```
