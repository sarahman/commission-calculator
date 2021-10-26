# Commission Calculator
It's very simple weekly commission calculator package.
This package read transaction from a CSV file then apply Pre define commission rules.

## Installation

Clone the repository. Make sure you have `docker` installed in your machine. Now run the following commands -

```bash
docker build -t sarahman-commission-calculator .
docker run -dtv "$(pwd):/var/www/html" --name sarahman-commission-calculator sarahman-commission-calculator

docker exec sarahman-commission-calculator sh -l -c 'composer install && cp .env.example .env'
docker exec sarahman-commission-calculator sh -l -c 'php script.php input.csv'
```

## Tests

```bash
docker exec sarahman-commission-calculator sh -l -c 'bin/phpunit'
```

## PHP-CS Check

```bash
docker exec sarahman-commission-calculator sh -l -c 'bin/paysera-php-cs-fixer fix --dry-run -v'
```
