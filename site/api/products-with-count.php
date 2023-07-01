<?php

use models\Product;

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;
use repositories\ProductRepository;

class GetProductsWithCountController extends BaseController
{
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    protected function onGet()
    {
        $result = $this->productRepository->getWithCount($_GET);
        
        if ($result)
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }


}

GetProductsWithCountController::Create()->HandleRequest();
?>