# GoedGepickt Webhook for Magento 2
GoedGepickt Extension for Order Webhook. 
 
## Installation: 
This project can easily be installed through Composer.

```
composer require georock/webhook
bin/magento module:enable GoedGepickt_Webhook
bin/magento setup:upgrade
```
## OR 

Download the zip file to your magento 2 directory as follows:

./app/code/GoedGepickt/Webhook

and run following commands in the terminal.

```
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```


## Activate module
1. Log onto your Magento 2 admin account and navigate to Stores > Configuration > Webhook Extensions > Integration
2. Fill out the general configuration information:
    + Active: Yes
    + Webhook Url: Your domain for recieving order data. 
    + Webhook Key: Copy and paste in this field. 
    
Orders will now be pushed to Webhook Domain immediately. 

## Uninstall: 

```
composer remove georock/webhook
```