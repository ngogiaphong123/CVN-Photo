# Web photo server

## Requirements
- PHP version 8.2 or higher
### Install dependencies

```bash
composer install
```
### Create .env file
Please create `.env` file from `.env.example` file and change all the environment variables.
```
touch .env
cp .env.example .env
```
### Create database
Please create database with name `webphoto` or change database name in `.env` file. Then run DDL script from `server/Database/ddl.sql` file.

### Run server
```bash
composer start
```
