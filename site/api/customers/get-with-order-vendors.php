<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CustomerRepository as CustomerRepository;
    use repositories\OrderVendorRepository as OrderVendorRepository;

    class GetCustomersWithDetailsController extends BaseController
    {
        private CustomerRepository $customerRepository;
        private OrderVendorRepository $orderVendorRepository;

        public function __construct()
        {
            $this->customerRepository = new CustomerRepository();
            $this->orderVendorRepository = new OrderVendorRepository();
        }

        protected function onGet()
        {
            //достаем данные по всем клиентам
            $customers = $this->customerRepository->getWithDetails($_GET);

            //$orderVendorByCustomerId = $this->orderVendorRepository->getOrderVendorByCustomerId(20);
                    
            //var_dump($orderVendorByCustomerId); //выдает ВСЕ ЗАКАЗЫ одного клиента Array 


            //отдельно достаем данные по всем заказам каждого из клиентов и соединяем в один многомерный массив
            $orderVendorByCustomerId = [];
            for ($i = 0; $i  < count($customers); $i++) {
                if($customers[$i]['id']) {
                    //print_r($customers[$i]);
                    //достаем нужные данные по заказу каждого клиента
                    $orderVendorByCustomerId = $this->orderVendorRepository->getOrderVendorByCustomerId($customers[$i]['id']);
                    //проверка на наличие заказов у этого клиента
                    if (count($orderVendorByCustomerId) > 0) {
                        //echo ' Номер итерации ' . $i . ' id клиента ' . $customers[$i]['id'] . ' заказов в массиве ' . count($orderVendorByCustomerId) . ' ';
                        //print_r($customers[$i]);

                        //добавляем данные по заказам каждого клиента в ключ orders
                        $customers[$i]['orders'] = $orderVendorByCustomerId;
                    //если заказов нет, добавляем пустой ключ
                    } else {
                        $customers[$i]['orders'] = [];
                    }
                    
                }

            }

            //если в гет-параметрах не задан vendor_id, выдаем весь массив клиентов
            if (!isset($_GET['vendor_id'])) {
                $result = $customers;
            }


            //если стоит фильтр по vendor_id
            if (isset($_GET['vendor_id'])) {
                $vendorId = $_GET['vendor_id'];

                //инициализируем отдельный массив для клиентов с нужным vendor_id
                $customersByVendorId = [];

                //собираем всех клиентов, у которых в массиве есть нужный vendor_id
                for ($k = 0; $k < count($customers); $k++) {
                    //проверка на наличие заказов у клиента
                    if (count($customers[$k]['orders']) > 0) {
                        //print_r($customers[$k]);

                        //проверка на совпадение vendor_id внутри заказов
                        for ($l = 0; $l < count($customers[$k]['orders']); $l++) {
                            //print_r($customers[$k]['orders'][$l]);
                            if ($customers[$k]['orders'][$l]->vendor_id == $vendorId) {
                                array_push($customersByVendorId, $customers[$k]);
                                break;
                            }
                        }

                    }
    
                }
                //выдаем массив только из клиентов, у которых есть нужный vendor_id
                $result = $customersByVendorId;
            }

            //echo json_encode($orderVendorByCustomerId, JSON_UNESCAPED_UNICODE);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

    }

    GetCustomersWithDetailsController::Create()->HandleRequest();
?>