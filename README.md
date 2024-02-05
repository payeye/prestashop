# Prestashop plugin

### How to build .zip
```shell
composer install --no-dev --optimize-autoloader
composer archive --format=zip --dir . --file payeye
mkdir payeye
unzip payeye.zip -d payeye
rm payeye.zip
zip -r payeye.zip payeye
```

### How to find bugs in codebase (https://github.com/phpstan/phpstan)
- composer run phpstan

### Version 0.1.0-RC
- Added positioning of the widget
- Added healthcheck endpoint
- Upgraded payeye/lib to the latest version.

### Version 0.0.39
- Compatibility with PrestaShop 1.7.4 and higher 
- Fix CCC - smart cache for JavaScript

### Version 0.0.38
- add different types of cart (STANDARD, VIRTUAL, MIXED) 
- payeye/lib up to 1.18.17

### Version 0.0.37
- fix widget prices
- fix prices with tax
- fix refresh cart hook
- add shop country validation in back office

### Version 0.0.36
- Compatibility with PrestaShop 1.7.6 and higher

### Version 0.0.35
- Fix nullable shipping id
- Add widget on-click
- Fix empty customer->mail
- Fix prices after scanning without delivery method