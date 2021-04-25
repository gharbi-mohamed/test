# Mage2 Module MGH Warehouse

    ``mgh/module-warehouse``

- [Main Functionalities](#markdown-header-main-functionalities)
- [Installation](#markdown-header-installation)
- [Configuration](#markdown-header-configuration)
- [Specifications](#markdown-header-specifications)
- [Attributes](#markdown-header-attributes)

## Main Functionalities

Test warehouse export

## Installation

\* = in production please use the `--keep-generated` option

### Type 1: Zip file

- Unzip the zip file in `app/code/MGH`
- Enable the module by running `php bin/magento module:enable MGH_Warehouse`
- Apply database updates by running `php bin/magento setup:upgrade`
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs

- OR use local path :
  - edit your composer.json and repo like following : 
  <pre>
     "repositories": [
          {
              "type": "path",
              "url": "/full/or/relative/path/to/MGH/Warehouse"
          }
        ]
  </pre>

- Add the composer repository to the configuration by
- Install the module composer by running `composer require mgh/module-warehouse`
- enable the module by running `php bin/magento module:enable MGH_Warehouse`
- apply database updates by running `php bin/magento setup:upgrade`
- Flush the cache by running `php bin/magento cache:flush`

## Configuration

- api_url (warehouse/api/api_url)

- api_key (warehouse/api/api_key)

- api_secret (warehouse/api/api_secret)

- live_notification (warehouse/export/live_notification)

- order_export_statuses (warehouse/export/order_export_statuses)

## Specifications

- Console Command
    - WarehouseExportOrders

- Cronjob
    - mgh_warehouse_exportwarehouse

## Attributes



