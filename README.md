A custom image processing module for [tradefurniturecompany.co.uk](https://www.tradefurniturecompany.co.uk) (Magento 2).  

## How to install
```             
sudo service crond stop
sudo service nginx stop                
sudo service php-fpm stop
bin/magento maintenance:enable
rm -rf composer.lock
composer clear-cache
composer require tradefurniturecompany/image:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/*
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_GB
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme TradeFurnitureCompany/default \
	-f en_GB 
sudo service php-fpm start
sudo service nginx start
bin/magento maintenance:disable
sudo service crond start
```

## How to upgrade
```              
sudo service crond stop
sudo service nginx stop                
sudo service php-fpm stop
bin/magento maintenance:enable
composer remove tradefurniturecompany/image
rm -rf composer.lock
composer clear-cache
composer require tradefurniturecompany/image:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/*
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_GB
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme TradeFurnitureCompany/default \
	-f en_GB
sudo service php-fpm start
sudo service nginx start
bin/magento maintenance:disable 
sudo service crond start
```