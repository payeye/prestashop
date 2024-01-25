# Prestashop plugin

# Before develop run command in root module:
- composer install
- composer dump-autoload

Run standard code:
- composer run cs-fix

Run PHPStan - PHP Static Analysis Tool (https://github.com/phpstan/phpstan)
- composer run phpstan

# Version 0.0.39
- Compatibility with PrestaShop 1.7.4 and higher 
- Fix CCC - smart cache for JavaScript

# Version 0.0.38
- add different types of cart (STANDARD, VIRTUAL, MIXED) 
- payeye/lib up to 1.18.17

# Version 0.0.37
- fix widget prices
- fix prices with tax
- fix refresh cart hook
- add shop country validation in back office

# Version 0.0.36
- Compatibility with PrestaShop 1.7.6 and higher

# Version 0.0.35
- Fix nullable shipping id
- Add widget on-click
- Fix empty customer->mail
- Fix prices after scanning without delivery method