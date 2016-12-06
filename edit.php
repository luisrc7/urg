<?php
/**
 * @file
 * Edit specific product.
 */

// Require the controller file.
require '/lib/controller.php';

$product_id = isset($_GET['product']) ? $_GET['product'] : $_POST['product_id'];

$existing_product = retrieve_product_by_id($product_id);

$form_values = array();

get_values_from_product($form_values, $existing_product);

if (isset($_POST['submit'])) {
  $form_values = validate_form($_POST, $_FILES);

  if (is_valid_form($form_values)) {
    $params = build_params($form_values['name'], $form_values['sku'], $form_values['price'], $form_values['description'], $form_values['image']);
    $product = edit_product($product_id, $params);
    if (is_array($product)) {
      if (file_exists($form_values['image'])) unlink($form_values['image']);
      $success = TRUE;
    }
    else {
      $errors = TRUE;
    }
  }
}

include_once '/header.html';
?>

<?php if (!$errors): ?>

  <?php if ($success): ?>
    <div class="alert alert-success fade in alert-dismissable">
      <strong>Success!</strong> The Product <?php echo $product['title']; ?> was updated.
    </div>
  <?php endif; ?>

  <form class="form-horizontal" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  enctype="multipart/form-data">
    <fieldset>

      <!-- Form Name -->
      <legend>Add New Product</legend>

      <!-- Text input-->
      <div class="form-group">
        <label class="col-md-4 control-label" for="name">Name</label>
        <div class="col-md-4">
          <input id="name" name="name" placeholder="Puzzel" class="form-control input-md" required="" type="text" value="<?php echo $form_values['name']; ?>">
          <div class="error"><?php echo $form_values['nameErr'];?></div>
        </div>
      </div>

      <!-- Text input-->
      <div class="form-group">
        <label class="col-md-4 control-label" for="sku">SKU</label>
        <div class="col-md-4">
          <input id="sku" name="sku" placeholder="RFT25-332" class="form-control input-md" required="" type="text" value="<?php echo $form_values['sku']; ?>">
          <div class="error"><?php echo $form_values['skuErr']; ?></div>
        </div>
      </div>

      <!-- Text input-->
      <div class="form-group">
        <label class="col-md-4 control-label" for="price">Price</label>
        <div class="col-md-4">
          <input id="price" name="price" placeholder="35.54" class="form-control input-md" required="" type="text" value="<?php echo  $form_values['price']; ?>">
          <div class="error"><?php echo $form_values['priceErr']; ?></div>
        </div>
      </div>

      <!-- File Button -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="image">Image</label>
        <div class="col-md-4">
          <?php if (!empty($form_values['image'])): ?>
            <img class="img-responsive img-rounded" src="<?php print $form_values['image'] ?>"/>
            <input id="img-hidden" name="img-hidden" class="form-control" type="hidden" value="<?php echo $form_values['image']; ?>">
          <?php endif; ?>
          <input id="image" name="image" class="input-file" type="file">
          <div class="error"><?php echo $form_values['imageErr']; ?></div>
        </div>
      </div>

      <!-- Textarea -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="description">Description</label>
        <div class="col-md-4">
          <textarea class="form-control" id="description" name="description"><?php echo $form_values['description']; ?></textarea>
          <div class="error"><?php echo $form_values['descriptionErr']; ?></div>
        </div>
      </div>

      <!-- Hidden fields. -->
      <input id="product_id" name="product_id" class="form-control" type="hidden" value="<?php echo $product_id; ?>">

      <!-- Button -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="submit"></label>
        <div class="col-md-4">
          <button id="submit" name="submit" class="btn btn-primary">Add Product</button>
        </div>
      </div>

    </fieldset>
  </form>
<?php else: ?>
  <div class="alert alert-danger">
    <strong>Error!</strong> <?php print $product; ?>
  </div>
<?php endif; ?>

</body>
</html>