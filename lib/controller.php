<?php
/**
 * @file
 * Code and functions to interact with the Shopify API.
 */

/** Require API library **/
// using existing library: https://github.com/cmcdonaldca/ohShopify.php/blob/7ee7a344ca83518a0560ba585d4f8deab65bf5cd/shopify.php
require 'ohshopify/shopify.php';

/** Shopify keys **/
define('SHOPIFY_API_KEY','6b12e2c3d88557ff0d62d03b183fdf58');
define('SHOPIFY_PASS','9a006be903d61ddefe6f8b0ebd3ea748');
define('SHOPIFY_SHOP','urg-test.myshopify.com');

/**
 * Retrieve all products.
 * @return array|mixed|string
 */
function retrieve_all_products() {
  $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_PASS, TRUE);
  try
  {
    return $shopifyClient->call('GET', '/admin/products.json');
  }
  catch (ShopifyApiException $e)
  {
    return '<span class="error">' . $e->getResponse()['errors'] . '</span>';
  }
}

/**
 * Retrieve one product.
 * @param $product_id
 * @return array|mixed|string
 */
function retrieve_product_by_id($product_id) {
  $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_PASS, TRUE);
  try
  {
    return $shopifyClient->call('GET', '/admin/products/' . $product_id . '.json');
  }
  catch (ShopifyApiException $e)
  {
    return '<span class="error">' . $e->getResponse()['errors'] . '</span>';
  }
}

/**
 * Add a product to the shop.
 * @param $params
 * @return array|mixed|string
 */
function add_product($params) {
  $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_PASS, TRUE);
  try
  {
    return $shopifyClient->call('POST', '/admin/products.json', $params);
  }
  catch (ShopifyApiException $e)
  {
    return '<span class="error">' . $e->getResponse()['errors'] . '</span>';
  }
}

/**
 * Edit existing Prduct.
 * @param $product_id
 * @param $params
 * @return array|mixed|string
 */
function edit_product($product_id, $params) {
  $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_PASS, TRUE);
  $params['product']['id'] = $product_id;
  try
  {
    return $shopifyClient->call('PUT', '/admin/products/' . $product_id . '.json', $params);
  }
  catch (ShopifyApiException $e)
  {
    return '<span class="error">' . $e->getResponse()['errors'] . '</span>';
  }
}

/**
 * Build the parameters for Add / Edit
 * @param $name
 * @param $sku
 * @param $price
 * @param string $description
 * @param string $image
 * @return array
 */
function build_params($name, $sku, $price, $description = '', $image = '') {
  return array(
    "product" => array(
      "title" => $name,
      "body_html" => $description,
      "vendor" => "URG Test",
      "product_type" => "API Product",
      "variants" => array(
        array(
          "price" => $price,
          "sku" => $sku,
        ),
      ),
      "images" => array(
        array(
          "src" => $image,
        ),
      ),
    ),
  );
}

/**
 * Validate the form fields.
 */
function validate_form($post, $file) {
  if (!isset($post["submit"])) return FALSE;
  $return = array();
  $return['name'] = '';
  $return['sku'] = '';
  $return['price'] = '';
  $return['description'] = '';
  $return['image'] = '';
  $return['nameErr'] = '';
  $return['skuErr'] = '';
  $return['priceErr'] = '';
  $return['imageErr'] = '';
  $return['descriptionErr'] = '';

  $target_dir = "uploads/";
  $target_file = $target_dir . basename($file["image"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
  if(isset($post["submit"]) && !empty($file["image"]["tmp_name"])) {
    $check = getimagesize($file["image"]["tmp_name"]);
    if($check !== false) {
      $uploadOk = 1;
    } else {
      $return['imageErr'] = "File is not an image.";
      $uploadOk = 0;
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
      && $imageFileType != "gif" ) {
      $return['imageErr'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.\n";
      $uploadOk = 0;
    }
    if ($uploadOk == 0) {
      $return['imageErr'] = 'Error uploading the image.';
    } else {
      if (move_uploaded_file($file["image"]["tmp_name"], $target_file)) {
        $return['image'] = $target_file;
      } else {
        $return['imageErr'] = 'Error uploading the image.';
      }
    }
  } elseif (!empty($post["img-hidden"]) && file_exists($post["img-hidden"])) {
    $return['image'] = $post["img-hidden"]; // Set old image.
  }

//var_dump($image);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($post["name"])) {
      $return['nameErr'] = "Name is required";
    } else {
      $return['name'] = test_input($post["name"]);
    }

    if (empty($post["sku"])) {
      $return['skuErr'] = "SKU is required";
    } else {
      $return['sku'] = test_input($post["sku"]);
    }

    if (empty($post["price"])) {
      $return['priceErr'] = "Price is required";
    } else {
      $return['price'] = test_input($post["price"]);
      // check if URL address syntax is valid (this regular expression also allows dashes in the URL)
      if (!is_numeric($return['price']) || $return['price'] <= 0) {
        $return['priceErr'] = "Invalid price, please enter a valid positive number.";
      }
    }

    if (empty($post["description"])) {
      $return['description'] = "";
    } else {
      $return['description'] = test_input($post["description"]);
    }

  }
  return $return;
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function is_valid_form($form_values) {
  if (
    is_array($form_values) &&
    empty($form_values['nameErr']) &&
    empty($form_values['skuErr']) &&
    empty($form_values['priceErr']) &&
    empty($form_values['imageErr']) &&
    empty($form_values['descriptionErr'])
  ) {
    return TRUE;
  }
  return FALSE;
}

function get_values_from_product(&$form_values, $product) {
  $form_values['name'] = $product['title'];
  $form_values['sku'] = $product['variants']['0']['sku'];
  $form_values['price'] = $product['variants']['0']['price'];
  $form_values['description'] = $product['body_html'];
  $form_values['image'] = $product['image']['src'];
}