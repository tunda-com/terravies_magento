## Main Functionalities
Terravives custom Fee

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Terravives`
 - Enable the module by running `php bin/magento module:enable Terravives_Fee`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require terravives/module-fee`
 - enable the module by running `php bin/magento module:enable Terravives_Fee`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - Accept Fees (terravives_fees/main/enable_fees)

 - Default Description (terravives_fees/main/default_description_fees)

 - Fee Amount Placeholder (terravives_fees/main/fees_amount_placeholder)

 - Api Url (terravives_fees/general/api_url)

 - Api Key (terravives_fees/general/api_key)

 - Add Product Data (terravives_fees/general/add_product_data)

 - Add Product Categories (terravives_fees/general/add_product_categories)


## Attributes

 - Sales - terravives_fee_invoiced (terravives_fee_invoiced)

 - Sales - base_terravives_fee_invoiced (base_terravives_fee_invoiced)

 - Sales - terravives_fee_refunded (terravives_fee_refunded)

 - Sales - base_terravives_fee_refunded (base_terravives_fee_refunded)

 - Sales - terravives_fee_cancelled (terravives_fee_cancelled)

 - Sales - base_terravives_fee_cancelled (base_terravives_fee_cancelled)

 - Sales - terravives_fee_amount (terravives_fee_amount)

 - Sales - base_terravives_fee_amount (base_terravives_fee_amount)

 - Sales - terravives_fee_tax_amount (terravives_fee_tax_amount)

 - Sales - base_terravives_fee_tax_amount (base_terravives_fee_tax_amount)

 - Sales - terravives_fee_details (terravives_fee_details)