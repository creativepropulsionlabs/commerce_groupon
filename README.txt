INTRODUCTION
----------

This module integrate groupon merchant with drupal.

REQUIREMENTS
------------

This module requires the following modules:

 * Commerce shipping (https://www.drupal.org/project/commerce_shipping)

INSTALLATION
------------

There is no special requirements for install process.

CONFIGURATION
-------------

* Configure the user permissions in Administration » People » Permissions:

   - Administer commerce_groupon configuration

* Configuration page is /admin/commerce/config/groupon

API RELATED DESCRIPTION
------------------------

Groupon orders and Groupon purchase orders are creating by cron.
After module install you should get supplier ID and token from groupon
and configure it /admin/commerce/config/groupon also you should select
order type which will be used as groupon order.

System Cron should be configured to 30 min cron run period or less.

Groupon orders you can find in the standard list of commerce orders  using
"Groupon order" field for filtering.

Api link is https://scm.commerceinterface.com/api-doc/v4/

FLOW
-----

* Module periodically calls /get_orders and create local order.
* For each imported line item (order item in terms of drupal commerce) /mark_exported would be  called
* When an order is created on the drupal side manager should process the standard delivery process.
* When manager updates info about tracking code in order shipping information (shipments entity on the drupal side)
module notifies Groupon about tracking code change  using /tracking_notification
* If the order was purchased on Groupon side purchased order has been created and module fetches this info
from /purchase_orders endpoint by cron automatically for each order.
For each order endpoint /purchase_orders/acknowledgement automatically calls by cron.
