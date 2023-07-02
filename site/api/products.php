<?php
    ini_set('post_max_size', '25M');
    ini_set('upload_max_filesize', '20M');

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
            $result = $this->productRepository->get($_GET);
            
            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        protected function onPost()
        {        
            $post = json_decode(file_get_contents('php://input'), true);
            if (isset($post['photoFileName']) && isset($post['photoFileData']))
                $post['photo'] = $this->uploadBase64File($post['photoFileName'], $post['photoFileData']);

            if (isset($post['id']))
            {
                $this->productRepository->updateById($post);
                return;
            }

            $this->productRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->productRepository->removeById($_GET);
        }

        private function uploadBase64File($fileName, $fileContent): ?string
        {           
            $path = '/upload/';
            $fsPath = $_SERVER['DOCUMENT_ROOT'] . $path;
            if (!is_dir($fsPath))
                mkdir($fsPath);

            $ext = pathinfo($fileName, PATHINFO_EXTENSION);

            if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png')
                return null;

            $fileName = md5(time()) . '.' . $ext;
            $fileContent = preg_replace('/^data:image\/\w+;base64,/', '', $fileContent); 
            
            $decodedContent = base64_decode(rawurldecode($fileContent));

            file_put_contents($fsPath . $fileName, $decodedContent);

            return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'] . $path . $fileName;
        }
    }

    ProductsController::Create()->HandleRequest();
?>