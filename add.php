<?php
/**
 * @file
 * Add new Product to Shop.
 */

// Require the controller file.
require 'lib/controller.php';

$form_values = validate_form($_POST, $_FILES);
$product = FALSE;
if (is_valid_form($form_values)) {
  $params = build_params($form_values['name'], $form_values['sku'], $form_values['price'], $form_values['description'], $form_values['image']);
  $product = add_product($params);
  if (is_array($product)) {
    unset($form_values['name']);
    unset($form_values['sku']);
    unset($form_values['price']);
    unset($form_values['description']);
    if (file_exists($form_values['image']))
      unlink($form_values['image']);
    unset($form_values['image']);
  }
}
include_once 'header.html';
?>


<?php if ($product && is_array($product)): ?>
  <div class="alert alert-success fade in alert-dismissable">
    <strong>Success!</strong> The Product <?php echo $product['title']; ?> was added to the Shop.
  </div>
<?php endif; ?>
<?php if ($product && !is_array($product)): ?>
  <div class="alert alert-danger fade in alert-dismissable">
    <strong>Error!</strong> <?php print $product; ?>
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

    <!-- Button -->
    <div class="form-group">
      <label class="col-md-4 control-label" for="submit"></label>
      <div class="col-md-4">
        <button id="submit" name="submit" class="btn btn-primary">Add Product</button>
      </div>
    </div>

  </fieldset>
</form>

</body>
</html>