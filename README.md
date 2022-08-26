<p align="center">
    <img src="https://banners.beyondco.de/API%20App.png?theme=light&packageManager=&packageName=by+Tom+Benevides&pattern=architect&style=style_1&description=A+simple+API+REST+app+with+PHP&md=1&showWatermark=0&fontSize=100px&images=document-add" width="600" alt="PHP-Video-Maker">
</p>

------

## Prerequisites

- PHP 8.1
- PostgreSQL

## Installation

- Clone the repository
- Install de dependencies running `composer install`
- Run the migrations with `./vendor/bin/doctrine-migrations migrate --configuration ./src/Config/migrations.php --db-configuration ./src/Config/database.php`
- Up the server with `php -S localhost:8000 -t public/index.php`

### docker

If you want to use docker, you can make use of the `build` script in the root of the project. It will run simplier commands to up the server:

- `./build composer install` to install the dependencies
- `./build migrations migrate` to run the migrations
- `./build serve` to up the server

p.s. The `build` script will only run the application in container. To run the migrations, you need to have the database up and running.

## Usage

The API has several endpoints to manage users. The user has to types: *admin* and *user*. The *admin* can do everything that the *user* can do and more. The endpoints and their details are listed below:

- `POST /login` to authenticate the user
  - who can use: *admin* and *user*
  - headers: `Content-Type: application/json`
  - payload: `{email: string, password: string, code: string (if MFA is enabled)}`
  - returns: `{token: string}`
  
- `POST /register` to register a new user
  - who can use: *admin* and *user*
  - headers: `Content-Type: application/json`
  - payload: `{name: string, email: string, password: string}`
  - returns: `{message: string}`
  
- `GET /enable-mfa` to enable the MFA authentication
  - who can use: *admin* and *user*
  - headers: `Content-Type: application/json|Authorization: <token>`
  - payload: `{}`
  - returns: `{secret: string, qrCode: string}`

- `POST /verify-mfa` to verify the MFA authentication
  - who can use: *admin* and *user*
  - headers: `Content-Type: application/json|Authorization: <token>`
  - payload: `{code: string}`
  - returns: `{message: string}`
  
- `POST /users` to create a new user
  - who can use: *admin*
  - headers: `Content-Type: application/json|Authorization: <token>`
  - payload: `{name: string, email: string, password: string, isAdmin: boolean}`
  - returns: `{user: object}`
  
- `PUT /users/{id}` to update a user name
  - who can use: *admin* and *user*
  - headers: `Content-Type: application/json|Authorization: <token>`
  - payload: `{name: string}`
  - returns: `{user: object}`

- `POST /users/{id}/deactivate` to deactivate a user
  - who can use: *admin*
  - headers: `Content-Type: application/json|Authorization: <token>`
  - payload: `{}`
  - returns: `{message: string}`

- `POST /users/{id}/activate` to activate a user
  - who can use: *admin*
  - headers: `Content-Type: application/json|Authorization: <token>`
  - payload: `{}`
  - returns: `{message: string}`