<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');


    use abstraction\BaseController;
    use repositories\ProductRepository;

    class GetProductsWithDetailLanguageController extends BaseController
    {
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->productRepository = new ProductRepository();
        }

        protected function onGet()
        {
            // выудим из базы массив товаров по условиям гет-запроса
            $products = $this->productRepository->getWithDetails($_GET);

            // в зависимости от выбранного языка, запишем для каждого товара
            // в поле name_language и description_language актуальные значения
            if(isset($_GET['language'])) {
                if($_GET['language'] == '1') {
                    // если выбран русский язык
                    echo 'русский';

                    for($i=0; $i < count($products); $i++) {
                        if($products[$i]['name'] !== null && $products[$i]['name'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name'];
                            $products[$i]['description_language'] =  $products[$i]['description']; 

                        }  else if ($products[$i]['name2'] !== null && $products[$i]['name2'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name2'];
                            $products[$i]['description_language'] =  $products[$i]['description2']; 

                        }  else if ($products[$i]['name3'] !== null && $products[$i]['name3'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name3'];
                            $products[$i]['description_language'] =  $products[$i]['description3']; 

                        } else {
                            $products[$i]['name_language'] =  $products[$i]['name'];
                            $products[$i]['description_language'] =  $products[$i]['description']; 
                        }
                    } 

                } else if($_GET['language'] == '2') {
                    // если выбран язык Оʻzbekcha
                    echo 'озбек';

                    for($i=0; $i < count($products); $i++) {
                        if($products[$i]['name2'] !== null && $products[$i]['name2'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name2'];
                            $products[$i]['description_language'] =  $products[$i]['description2']; 

                        }  else if ($products[$i]['name3'] !== null && $products[$i]['name3'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name3'];
                            $products[$i]['description_language'] =  $products[$i]['description3']; 

                        }  else if ($products[$i]['name'] !== null && $products[$i]['name'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name'];
                            $products[$i]['description_language'] =  $products[$i]['description']; 

                        } else {
                            $products[$i]['name_language'] =  $products[$i]['name2'];
                            $products[$i]['description_language'] =  $products[$i]['description2']; 
                        }
                    } 

                } else if($_GET['language'] == '3') {
                    // если выбран язык Ўзбекча
                    echo 'Ўзбекча';

                    for($i=0; $i < count($products); $i++) {
                        if($products[$i]['name3'] !== null && $products[$i]['name3'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name3'];
                            $products[$i]['description_language'] =  $products[$i]['description3']; 

                        }  else if ($products[$i]['name2'] !== null && $products[$i]['name2'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name2'];
                            $products[$i]['description_language'] =  $products[$i]['description2']; 

                        }  else if ($products[$i]['name'] !== null && $products[$i]['name'] !== '') {

                            $products[$i]['name_language'] =  $products[$i]['name'];
                            $products[$i]['description_language'] =  $products[$i]['description']; 

                        } else {
                            $products[$i]['name_language'] =  $products[$i]['name3'];
                            $products[$i]['description_language'] =  $products[$i]['description3']; 
                        }
                    } 
                }
            } else {
                for($i=0; $i < count($products); $i++) {
                    $products[$i]['name_language'] =  $products[$i]['name'];
                    $products[$i]['description_language'] =  $products[$i]['description'];
                } 
            }
                
            


            echo json_encode($products, JSON_UNESCAPED_UNICODE);
        }
    }

    GetProductsWithDetailLanguageController::Create()->HandleRequest();
?>