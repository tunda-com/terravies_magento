# Terravives Custom Fee Module

## Installation

1. **Unzip the Module:**
   - Extract the contents of the zip file into `app/code/Terravives`.

2. **Enable the Module:**
   - Run the following command to enable the module:  
     ```bash
     php bin/magento module:enable Terravives_Fee
     ```

3. **Apply Database Updates:**
   - Execute the following command to apply any necessary database updates:  
     ```bash
     php bin/magento setup:upgrade
     ```

4. **Flush Cache:**
   - Flush the Magento cache by running:  
     ```bash
     php bin/magento cache:flush
     ```

---

## Configuration

You can configure the module through the Magento Admin Panel under the `Stores > Configuration > Terravives Fee` section.

- **Enable Fees (`terravives_fees/main/enable_fees`):**  
  Enable or disable the custom fee functionality.

- **Default Description (`terravives_fees/main/default_description_fees`):**  
  Set the default description that will appear alongside the fee in the checkout process.

- **Fee Amount Placeholder (`terravives_fees/main/fees_amount_placeholder`):**  
  Define the placeholder text for the fee amount input field.

- **API Url (`terravives_fees/general/api_url`):**  
  Set the API endpoint URL for interacting with Terravives.

- **API Key (`terravives_fees/general/api_key`):**  
  Enter the API key for authenticating with the Terravives API.

- **Add Product Data (`terravives_fees/general/add_product_data`):**  
  Enable this option to include product details in the API call.  
  **Note:** We do not recommend enabling this option as it can slow down the API response time.

- **Add Product Categories (`terravives_fees/general/add_product_categories`):**  
  Enable this option to include product categories in the API call.  
  **Note:** Similar to adding product data, enabling this option may cause slower performance and is not recommended for most use cases.

---

## Checkout and Donation Feature

- The **Terravives Custom Fee** allows customers to make a donation to a charity project during checkout. They can select the project they wish to donate to and view the total amount of their donation.
- Every client must configure the API connection by entering the API URL and API Key in the configuration settings.

**Important:**  
If you choose to include product information in the API call (by enabling `Add Product Data` and `Add Product Categories`), please be aware that this may slow down the response time and affect the checkout process performance.

