A custom image processing module for [tradefurniturecompany.co.uk](https://www.tradefurniturecompany.co.uk) (Magento 2).  

## How to install
```             
sudo service crond stop
sudo service nginx stop                
sudo service php-fpm stop
bin/magento maintenance:enable
rm -rf composer.lock
composer clear-cache
composer2 require --ignore-platform-reqs --no-plugins tradefurniturecompany/image:*
composer update
rm -rf var/di var/generation generated/*
bin/magento setup:upgrade
bin/magento cache:enable
bin/magento setup:di:compile
bin/magento cache:clean
rm -rf pub/static/* var/cache var/page_cache var/view_preprocessed
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_GB
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme TradeFurnitureCompany/default \
	-f en_GB
bin/magento cache:clean	 
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
composer2 require --ignore-platform-reqs --no-plugins tradefurniturecompany/image:*
composer update
rm -rf var/di var/generation generated/*
bin/magento setup:upgrade
bin/magento cache:enable
bin/magento setup:di:compile
bin/magento cache:clean
rm -rf pub/static/* var/cache var/page_cache var/view_preprocessed
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_GB
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme TradeFurnitureCompany/default \
	-f en_GB
bin/magento cache:clean
sudo service php-fpm start
sudo service nginx start
bin/magento maintenance:disable 
sudo service crond start
```
