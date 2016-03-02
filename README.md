# Backup WP
Backup WordPress files and database

## Requirements
* PHP 5.4
* tar command
* mysqldump command

## Usage
* Rename db-sample.ini to db.ini and config MySQL settings.

## Example
```php
$backup_wp = new BackupWP('/path/to/wp_install_dir/', 'wp_database');
$backup_wp->createArchive('/archive/save/filepath.tar.gz');
$backup_wp->dumpDatabase('/dump/save/filepath.sql');
```
