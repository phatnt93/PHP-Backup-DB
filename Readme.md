# Backup DB

Backup DB (Mariadb) to Google drive

## Technologies

- PHP 8.3
- Phalcon 5.9

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
