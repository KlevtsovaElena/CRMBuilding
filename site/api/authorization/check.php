<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\VendorRepository;

    class CheckController extends BaseController
    {
        private VendorRepository $vendorRepository;

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
        }

        protected function onPost()
        {

            $post = [];
            $post['token'] = $_POST['cookie'];
            $post['deleted'] = '0';

            $vendor = $this->vendorRepository->getWithDetails($post);

            // проверяем есть ли пользователь с таким токеном
            
            // если нет, то возвращаем на фронт ошибку
            if (!$vendor) {
                $response = [
                    'success' => false,
                    'error' => ''
                ];
                echo json_encode($response,  JSON_UNESCAPED_UNICODE);
                return;
            // если есть, то проверим админ ли это
            // если не админ, то он должен быть is_active, город его должен быть активным и неудалённым
            } else {
                $vendor = (object)$vendor[0];

                if($vendor->role <> '1') {
                    if ($vendor->city_active == '0' || $vendor->city_deleted == '1' || $vendor->is_active == '0') {
                        $response = [
                            'success' => false,
                            'error' => ''
                        ];
                        echo json_encode($response,  JSON_UNESCAPED_UNICODE);
                        return;
                    }
                }

                $response = [
                    'success' => true,
                    'profile' => [
                        'id' => $vendor->id,
                        'name' => $vendor->name,
                        'city_id' => $vendor->city_id,
                        'tg_id' => $vendor->tg_id,
                        'hash_string' => $vendor->hash_string,
                        'phone' => $vendor->phone,
                        'email' => $vendor->email,
                        'role' => $vendor->role,
                        'coordinates' => $vendor->coordinates,
                        'price_confirmed' => $vendor->price_confirmed,
                        'currency_dollar' => $vendor->currency_dollar,
                        'rate' => $vendor->rate,
                        'city_name' => $vendor->city_name,
                        'city_active' => $vendor->city_active,
                        'city_deleted' => $vendor->city_deleted,
                        'deleted' => $vendor->deleted,
                        'is_active' => $vendor->is_active

                    ],
                ];
                echo json_encode($response,  JSON_UNESCAPED_UNICODE);
            
            }

        }

        // protected function onGet() {

        //     $post = [];
        //     $post['token'] = $_GET['cookie'];
        //     // $post['city_active'] = '1';
        //     // $post['city_deleted'] = '0';
        //     // $post['deleted'] = '0';

        //     $vendor = $this->vendorRepository->getWithDetails($post);

        //     // проверяем есть ли пользователь с таким токеном
            
        //     // если нет, то возвращаем на фронт ошибку
        //     // если есть, то возвращаем на фронт его данные
        //     if (!$vendor) {
        //         $response = [
        //             'success' => false,
        //             'error' => ''
        //         ];
        //         echo json_encode($response,  JSON_UNESCAPED_UNICODE);
        //         return;
        //     } else {
        //         $vendor = (object)$vendor[0];

        //         if($vendor->role <> '1') {
        //             if ($vendor->city_active == '0' || $vendor->city_deleted == '1' || $vendor->is_active == '0' || $vendor->deleted == '1') {
        //                 $response = [
        //                     'success' => false,
        //                     'error' => ''
        //                 ];
        //                 echo json_encode($response,  JSON_UNESCAPED_UNICODE);
        //                 return;
        //             }
        //         }

        //         $response = [
        //             'success' => true,
        //             'profile' => [
        //                 'id' => $vendor->id,
        //                 'name' => $vendor->name,
        //                 'city_id' => $vendor->city_id,
        //                 'tg_id' => $vendor->tg_id,
        //                 'hash_string' => $vendor->hash_string,
        //                 'phone' => $vendor->phone,
        //                 'email' => $vendor->email,
        //                 'role' => $vendor->role,
        //                 'coordinates' => $vendor->coordinates,
        //                 'price_confirmed' => $vendor->price_confirmed,
        //                 'currency_dollar' => $vendor->currency_dollar,
        //                 'rate' => $vendor->rate,
        //                 'city_name' => $vendor->city_name,
        //                 'city_active' => $vendor->city_active,
        //                 'city_deleted' => $vendor->city_deleted,
        //                 'deleted' => $vendor->deleted,
        //                 'is_active' => $vendor->is_active

        //             ],
        //         ];
        //         echo json_encode($response,  JSON_UNESCAPED_UNICODE);
        //     }


        // }
    }

    CheckController::Create()->HandleRequest();
?>