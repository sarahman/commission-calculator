# Commission Calculator
It's very simple weekly commission calculator package.
This package read transaction from a CSV file then apply Pre define commission rules.

**How to install:**

* After cloning the project and installing `docker`, run

```properties
docker build --no-cache docker/app -t sarahman.com/commission-calculator
docker run -tdv "$(pwd):/var/www/html" --name sarahman-commission-calculator-app sarahman.com/commission-calculator
```

* Then run the following command to enter the docker container of the calculator app:

```properties
docker exec -it sarahman-commission-calculator-app sh
```

* Then run the following command

```properties
composer install
```

* Create ENV

```properties
composer run create-env
```

* Change Your Access KEY for Exchange rate API in the `.env` file.
Run project by

```properties
php script.php input.csv
```

or

```properties
php script.php
```

or

```properties
composer run script
```

**How to test:**

Run Unit Test by

```properties
composer run phpunit
```
