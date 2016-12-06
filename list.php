<?php
/**
 * @file
 * listing all products.
 */

// Require the controller file.
require '/lib/controller.php';

$products = retrieve_all_products();

include_once '/header.html';
?>

<?php if (is_array($products)): ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
      <tr>
        <th>ID#</th>
        <th>Name</th>
        <th>SKU</th>
        <th>Price</th>
        <th>Edit</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($products as $product): ?>
        <tr>
          <td><?php print $product['id']; ?></td>
          <td><?php print $product['title']; ?></td>
          <td><?php print $product['variants']['0']['sku']; ?></td>
          <td>£<?php print $product['variants']['0']['price']; ?></td>
          <td><a class="btn btn-primary" href="/edit.php?product=<?php print $product['id']; ?>">Edit</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="alert alert-danger">
    <strong>Error!</strong> <?php print $products; ?>
  </div>
<?php endif; ?>

</body>
</html>
