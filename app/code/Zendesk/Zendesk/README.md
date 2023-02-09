# Zendesk Integration Extension
   
   # Setup
   
   1. In your Zendesk Support account, generate an API token as described in the [Generating a new API token documentation](https://support.zendesk.com/hc/en-us/articles/226022787-Generating-a-new-API-token).
   2. In the Magento backend, click the _Zendesk_ top-level link in main navigation.
   3. Expand the _General_ section, then populate the following fields.
   	1. `Zendesk Domain`: The Zendesk support domain. For example, _yourdomain_.zendesk.com .
   	2. `Agent Email Address`: Your agent email address.
   	3. `Agent Token`: The Zendesk API token generated in step 1.
   4. Click the _Save Config_ button on the top-right.
   5. Once the page has reloaded, click the _Test Connection_ button in the _General_ section to confirm that the Zendesk API can be successfully accessed.
   
# Installation 
```
composer config repositories.zendesk/module-zendesk git git@bitbucket.org:classyllama/zendesk_zendesk.git
composer require zendesk/module-zendesk
```

##Zendesk Sunshine API Documentation

https://developer.zendesk.com/rest_api/docs/sunshine/introduction
Description

#Sunshine

The Magento store will need to listen to specific events in order to pass relevant data to Zendesk Sunshine. Magento will need to listen to the following:

 - Item added to shopping cart
 - Item removed from shopping cart
 - Refund/Return status change
 - Checkout Started
 - Customer updated or customer created
 - Customer deleted
 - Order placed or order updated
 - Order canceled
 - Order paid
 - Order shipped
