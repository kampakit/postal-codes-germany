<?php

namespace Kampakit\PostalCodesGermany\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kampakit\PostalCodesGermany\SearchInterface;

class AutocompleteController extends Controller
{
    private SearchInterface $searchEngine;

    public function __construct(SearchInterface $searchEngine)
    {
        $this->searchEngine = $searchEngine;
    }

    public function __invoke(Request $request)
    {
        $search = $request->search;
        if (empty($search)) {
            return response()->json(['results' => []]);
        }
        return response()->json([
            'results' => $this->searchEngine->autocompleteSearch($search)
        ]);
    }
}