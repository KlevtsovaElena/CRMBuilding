<?php
namespace models;

    enum OrderVendorStatus : int
    {
        case Created = 0;
        case Opened = 1;
        case Confirmed = 2;
        case Canceled = 3;
        case Delivered = 4;
    }

?>