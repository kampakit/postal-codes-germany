<?php

use Kampakit\PostalCodesGermany\Http\Controllers\AutocompleteController;

Route::get('/api/postal_codes_germany/autocomplete', AutocompleteController::class)
    ->name('postal_codes_germany.autocomplete');