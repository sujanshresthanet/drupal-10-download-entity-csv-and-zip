<?php

namespace Drupal\download_commerce_product\Controller;

use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Controller for the retrieval of commerce product data via CSV and ZIP file.
 */
class ProductsDdownloadController extends ControllerBase {

  /**
   * DownloadCsv function: Generates a CSV file download response.
   */
  public function DownloadCsv() {

    // Get the file path for the products CSV.
    $file_path = $this->ProductsCsvFile();

    // Set up HTTP headers for file download.
    $response = new Response();

    // Set the content type to indicate it's a CSV file.
    $response->headers->set("Content-Type", "text/csv");

    // Set the Content-Disposition header to trigger download and specify the file name.
    $response->headers->set(
    "Content-Disposition",
    'attachment; filename="products.csv"'
    );

    // Read the contents of the CSV file and set it as the response content.
    $response->setContent(file_get_contents($file_path));

    // Return the prepared response.
    return $response;
  }

  /**
   * DownloadZip function: Generates a zip file download response.
   */
  public function DownloadZip() {
    $file_path = $this->ProductsCsvFile();

    // File paths to include in the zip file.
    // You can add any other file paths to download them in the zip.
    $filePaths = [
      $file_path,
    ];

    // Create a temporary directory to store files.
    $tempDir = \Drupal::service('file_system')->realpath('public://');

    // Create a unique zip file name.
    $zipFileName = 'products-' . time() . '.zip';
    $zipFilePath = $tempDir . '/' . $zipFileName;

    // Create a new zip archive.
    $zip = new \ZipArchive();
    $zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    // Download each file and add it to the zip archive.
    foreach ($filePaths as $fileUrl) {
      // Download file content.
      $fileContent = file_get_contents($fileUrl);

      // Get file name from URL.
      $fileName = basename($fileUrl);

      // Add file to the zip archive.
      $zip->addFromString($fileName, $fileContent);
    }

    // Close the zip archive.
    $zip->close();

    // Prepare the response.
    $response = new BinaryFileResponse($zipFilePath);
    $response->setContentDisposition(
    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    $zipFileName
    );

    // Return the prepared response.
    return $response;

  }

  /**
   * Generates products CSV file.
   */
  public function ProductsCsvFile() {
    $product_data = [];
    $all_products = Product::loadMultiple();
    if ($all_products) {
      foreach ($all_products as $key => $product) {
        $product_id = $product->id();
        $variation_price = 0;

        // Get the variations of the product.
        $variations = $product->getVariations();

        // Check if there are any variations.
        if (!empty($variations)) {
          // Get the first variation.
          $first_variation = reset($variations);
          $variation_title = $first_variation->label();
          $variation_price = $first_variation
            ->getPrice()
            ->getNumber();
          $variation_price = round($variation_price, 2);
          $variation_price = number_format(
          $variation_price,
          2,
          ".",
          ""
          );
        }

        $product_data[$key]["Id"] = $product->id();
        $product_data[$key]["Title"] = $product->label();
        $product_data[$key]["Price"] = $variation_price;
      }
    }

    // Save CSV file as a managed file entity.
    $file_path = $this->saveArrayAsManagedFile($product_data, 'module');
    return $file_path;
  }

  /**
   * Custom function to convert array to CSV and save file as a managed file entity.
   */
  public function saveArrayAsManagedFile(array $data, $file_name) {
    $csv_data = "";
    $file_path = "";
    // Write CSV header.
    $csv_data .= implode(",", array_keys($data[1])) . PHP_EOL;
    // Write CSV data.
    foreach ($data as $row) {
      $csv_data .= implode(",", $row) . PHP_EOL;
    }

    // Create managed file entity.
    $file = \Drupal::service("file.repository")->writeData(
    $csv_data,
    "public://{$file_name}.csv",
    1
    );
    if ($file) {
      // Set the file as temporary.
      $file->setTemporary();

      // Set the file as permanent.
      // $file->setPermanent();
      // Save the changes.
      $file->save();

      $file_path = \Drupal::service(
      "file_url_generator"
      )->generateAbsoluteString($file->getFileUri());
    }

    // Return file path.
    return $file_path;
  }

}
