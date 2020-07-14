<?php

namespace Kampakit\PostalCodesGermany;

interface SearchInterface {
    public function autocompleteSearch(string $search): array;
}
