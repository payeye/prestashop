# Prestashop plugin

# Before develop run command in root module:
- composer install
- composer dump-autoload

Run standard code:
- composer run cs-fix

Run PHPStan - PHP Static Analysis Tool (https://github.com/phpstan/phpstan)
- composer run phpstan

# Version 0.0.35
- Fix nullable shipping id
- Add widget on-click
- Fix empty customer->mail
- Fix prices after scanning without delivery method