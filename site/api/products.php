<?php
ini_set('post_max_size', '25M');
ini_set('upload_max_filesize', '20M');

use models\Product;

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;
use repositories\ProductRepository;

class ProductsController extends BaseController
{
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    protected function onGet()
    {
        $result = isset($_GET['id']) ? $this->productRepository->getById($_GET['id']) :
            $this->productRepository->getAll();

        if ($result)
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    protected function onPost()
    {        
        if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['categoryId']))
            return;

        $photoUrl = null;
        if ($_FILES != null && $_FILES['photo'] != null)
            $photoUrl = $this->uploadFile($_FILES['photo']);

        $product = new Product();
        $product->name = $_POST['name'];
        $product->description = $_POST['description'];
        $product->categoryId = $_POST['categoryId'];
        $product->photo = $photoUrl;
        $product->article = $_POST['articleId'];
        $product->brandId = $_POST['brandId'];
        $product->vendorId = $_POST['vendorId'];
        $product->quantityAvailable = $_POST['quantityAvailable'];
        $product->price = $_POST['price'];
        $product->maxPrice = $_POST['maxPrice'];

        if (isset($_POST['id']))
        {
            $product->id = $_POST['id'];
            $this->productRepository->update($product);
            return;
        }

        $this->productRepository->add($product);
    }

    protected function onDelete()
    {
        if (!isset($_GET['id']))
            return;

        $this->productRepository->removeById($_GET['id']);
    }

    private function uploadFile($file): string
    {
        $path = '/upload/';
        $fsPath = $_SERVER['DOCUMENT_ROOT'] . $path;
        if (!is_dir($fsPath))
            mkdir($fsPath);

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png')
            return null;

        $fileName = md5(time()) . '.' . $ext;

        move_uploaded_file($file['tmp_name'], $fsPath . $fileName);

        return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'] . $path . $fileName;
    }
}

ProductsController::Create()->HandleRequest();
?>