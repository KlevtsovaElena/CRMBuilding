<?php
use repositories\VendorRepository;

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;
use repositories\OrderVendorRepository as OrderVendorRepository;

class OrderVendorsController extends BaseController
{
    private OrderVendorRepository $orderVendorRepository;
    private VendorRepository $vendorRepository;

    public function __construct()
    {
        $this->orderVendorRepository = new OrderVendorRepository();
        $this->vendorRepository = new VendorRepository();
    }

    protected function onGet()
    {
        $result = $this->orderVendorRepository->get($_GET);

        if ($result)
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    protected function onPost()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        if (isset($post['id'])) {
            $this->orderVendorRepository->updateById($post);

            if (isset($post['with_own_recalc']) && $post['with_own_recalc'] == true && isset($post['status']) && $post['status'] == 4) {
                $currentOrderVendor = $this->orderVendorRepository->get([
                    'id' => $post['id']
                ]);

                if (isset($currentOrderVendor) && !$currentOrderVendor->owns_accrued) {
                    try {
                        DbContext::getConnection()->beginTransaction();

                        $currentVendor = $this->vendorRepository->get([
                            'id' => $currentOrderVendor->vendor_id
                        ]);

                        $this->vendorRepository->updateById([
                            'id' => $currentVendor->id,
                            'owns' => (($currentOrderVendor->total_price / 100) * $currentVendor->percent) + $currentVendor->owns
                        ]);

                        $this->orderVendorRepository->updateById([
                            'id' => $currentOrderVendor->id,
                            'owns_accrued' => true
                        ]);

                        \DbContext::getConnection()->commit();
                    } catch (Exception $e) {
                        \DbContext::getConnection()->rollBack();
                        die($e);
                    }
                }
            }
            return;
        }

        $this->orderVendorRepository->add($post);
    }

    protected function onDelete()
    {
        if (isset($_GET['id']))
            $this->orderVendorRepository->removeById($_GET);
    }
}

OrderVendorsController::Create()->HandleRequest();
?>