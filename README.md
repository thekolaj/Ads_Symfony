# Ads with Symfony

## Setup

Just run `docker compose up`. Database will be created and fixtures loaded with the help of `init.sh` file.

## Usage

- Main page: http://localhost:8080/index.php
- Admin area: http://localhost:8080/index.php/admin

## Sample users

- normal: `user@example.com` / `Password123`
- normal: `hacker@example.com` / `Password123`
- admin: `admin@example.com` / `Password123`

## Project goals

- Use Security bundle and have a custom voter
- Learn the basics of EasyAdmin
- Make lists paginated
- Use Traits for created/updatedAt
- Try out Foundry bundle and Faker
- Write some Functional test
- Try out code quality tools
- Automate development setup

## Features/Results

### Automated dev setup

With the help of docker, the app performs all the necessary setup for you. 
It runs composer install, recreates a fresh database and loads fixtures.
Then, it does the same with the test database.

Had to figure out how to create shell scripts and run them using docker. 
Learned a lot about `command`/`CMD`/`ENTRYPOINT`

### Fixtures with dynamic random data

With the help of Foundry bundle and faker, we are creating a lot of data to use during development and tests.

Tricky parts were hashing a password in the factory and minimizing database calls.

### Admin panel

A simple implementation where admins can modify existing entities.

An extensive bundle with a lot of customization. Just established some basic familiarity with it.

### Automated tests

Simple functional test that use preloaded data from fixtures. 
`DAMADoctrineTestBundle` keeps our data consistent between tests with transactions.

Some basic happy path Functional test for every system, just to practice their creation.
Was not aiming for 100% code coverage or testing for edge cases to save time.
Big part of testing was creating and loading data for them.

### Auth system

User registration, login, logout, and remember me functionality. A user can edit/delete his own ads/comments. 
Admin can edit/delete every ad.

Managed to implement all the functionality I wanted with just one Voter and an interface.

### Code quality tools

Code is formated with `php-cs-fixer` and passes `phpstan` analysis on level: max.

Took quite a bit of time to set everything up.
Set up `php-cs-fixer` to run on save in PHP Storm from the Docker container.
`phpstan` does not come with good Symfony/Doctrine support out to the box.
With 3 extra extension and some configuration it now is working as intended.

### Pagination

### Created/updateAt

### Reusable forms and Data validation

## What was left out / possible improvements

- Beautiful UI.
- Change password, update password in Admin panel. 
- Admin: create new entities.
- Images on Ads.
- Turbo UX: create/update comments dynamically.
- User profiles for each user.
- Category system.

