
<?php

$brandsJson = file_get_contents("http://nginx/api/brands.php");
$brands = json_decode($brandsJson, true);
$categoriesJson = file_get_contents("http://nginx/api/categories.php");
$categories = json_decode($categoriesJson, true);
?>
// <!-- список -->
<p>Бренд</p>
<select id="brandId" name="brandId" value="" required>

    <?php foreach($brands as $brand) { ?>
        <option value="<?= $brand['id']; ?>"><?= $brand['brandName']; ?></option>
    <?php }; ?>

</select>

// <!-- список -->
<p>Категория</p>
<select id="categoryId" name="categoryId" value="" required>
    <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
    <?php foreach($categories as $category) { ?>
        <option value="<?= $category['id']; ?>"><?= $category['categoryName']; ?></option>
    <?php }; ?>
</select>