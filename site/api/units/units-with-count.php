<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\UnitRepository;

    class GetUnitsWithCountController extends BaseController
    {
        private UnitRepository $unitRepository;

        public function __construct()
        {
            $this->unitRepository = new UnitRepository();
        }

        protected function onGet()
        {
            $units = $this->unitRepository->get($_GET);
            $count = $this->unitRepository->getCountWithDetails($_GET);
            
            if (isset($_GET['id']) && $units)
                $units = [$units];

            echo json_encode([
                "count" => $count,
                "units" => $units ?? []
            ],
            JSON_UNESCAPED_UNICODE);
        }
    }

    GetUnitsWithCountController::Create()->HandleRequest();
?>