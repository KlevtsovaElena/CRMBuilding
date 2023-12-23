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

            if (isset($post['with_debt_recalc']) && $post['with_debt_recalc'] == true && isset($post['status']) && $post['status'] == 4) {
                $currentOrderVendor = $this->orderVendorRepository->get([
                    'id' => $post['id']
                ]);

                if (isset($currentOrderVendor) && !$currentOrderVendor->debt_accrued) {
                    try {
                        DbContext::getConnection()->beginTransaction();

                        $currentVendor = $this->vendorRepository->get([
                            'id' => $currentOrderVendor->vendor_id
                        ]);

                        $this->vendorRepository->updateById([
                            'id' => $currentVendor->id,
                            'debt' => (($currentOrderVendor->total_price / 100) * $currentVendor->percent) + $currentVendor->debt
                        ]);

                        $this->orderVendorRepository->updateById([
                            'id' => $currentOrderVendor->id,
                            'debt_accrued' => true
                        ]);

                        \DbContext::getConnection()->commit();
                    } catch (Exception $e) {
                        \DbContext::getConnection()->rollBack();
                        die($e);
                    }
                }
            }

            // если меняем статус на В доставке
            // то надо отправить уведомление клиенту
            if (isset($post['status']) && $post['status'] == 5 && isset($post['chat_id'])) {
                $message = 'Ваш заказ находится в доставке';
                file_get_contents('https://api.telegram.org/bot'.$_ENV['BOT_TOKEN'].'/sendMessage?chat_id=' . urlencode($post['chat_id']) . '&text=' . $message);
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