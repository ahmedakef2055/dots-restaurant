<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Temporary switch for system testing: when disabled, cashier order flows
    | do not enforce recipe/stock availability checks and do not deduct
    | inventory on paid status transitions.
    |
    */
    'cashier_inventory_recipe_link_enabled' => env('CASHIER_INVENTORY_RECIPE_LINK_ENABLED', false),
];
