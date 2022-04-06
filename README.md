# php-dry – Clone Detection for PHP

php-dry detects duplicated behaviour in your application, even if the duplicated passages are implemented completely
different to each other. Likely you should read a bit about the [theoretical background](documentation/theoretical_background.md)
for a better understanding.

## Run via Docker (recommended)

```bash
docker run -v {path_to_project}:/project leovie/php-dry -h
```

## Run via binary

Install via composer

```bash
composer require --dev leovie/php-dry
```

After installation, you can run php-dry via
```bash
vendor/bin/php-dry {path_to_project} -h
```

## Configuration
see [here](documentation/configuration.md)

## Thanks

Special thank you belongs to [queo GmbH](https://www.queo.de) for sponsoring the development and maintenance of php-dry.