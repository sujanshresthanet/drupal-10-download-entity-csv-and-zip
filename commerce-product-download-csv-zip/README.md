**Download Drupal Commerce Products**

**Overview**
------------

The **Download Drupal Commerce Products** module provides example routes and controllers for downloading products as CSV files and packaging multiple CSV files into a ZIP file. This README provides instructions on how to use the module and extend it for downloading other entities.

**Installation**
----------------

1.  Copy the **commerce-product-download-csv-zip** module directory to the **modules/custom** directory in your Drupal installation.
2.  Enable the module through the Drupal administrative interface or by running **drush en commerce-product-download-csv-zip**.

**Routes**
----------

### **Download Products as CSV**

*   Path: **/admin/commerce/download-csv**
*   Description: Downloads products as a CSV file.
*   Controller: **\\Drupal\\download\_commerce\_product\\Controller\\ProductCsvDownloadController::DownloadCsv**

### **Download Multiple Files as ZIP**

*   Path: **/admin/commerce/download-zip**
*   Description: Packages multiple CSV files into a ZIP file.
*   Controller: **\\Drupal\\download\_commerce\_product\\Controller\\ZipDownloadController::DownloadZip**

**Usage**
---------

1.  Navigate to the provided routes in your browser or make requests to these routes programmatically.
2.  Adjust parameters or extend controllers as needed for your specific use case.

**Extending for Other Entities**
--------------------------------

You can extend this module to download other entities as CSV or ZIP files by following these steps:

1.  Implement controllers for generating CSV files for your desired entities.
2.  Add corresponding routes in the **download\_commerce\_product.routing.yml** file.
3.  If needed, create controllers to package multiple entity files into a ZIP file.
