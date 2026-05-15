<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Thermal Printer Device Paths
    |--------------------------------------------------------------------------
    |
    | cashier  – receipt printer at the cash register
    | bar      – preparation ticket printer in the kitchen / bar area
    |
    | Override via .env:
    |   CASHIER_PRINTER_DEVICE=/dev/usb/lp0
    |   BAR_PRINTER_DEVICE=/dev/usb/lp1
    |
    */

    'cashier' => env('CASHIER_PRINTER_DEVICE', '/dev/usb/lp0'),

    // Temporarily using the same device as cashier for testing.
    // Set BAR_PRINTER_DEVICE=/dev/usb/lp1 in .env when the bar printer is ready.
    'bar'     => env('BAR_PRINTER_DEVICE', '/dev/usb/lp0'),

];
