<?php


namespace Kampakit\PostalCodesGermany;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Transliterator;
use PDO;

class SearchPostgres implements SearchInterface
{
    public function autocompleteSearch(string $search): array {
        preg_match_all('/\d+/', $search, $numbers);
        preg_match_all('/[^\d\s]+/',  $search, $words);
        $text_string = join(" ", $words[0]);
        // TODO: escape wildcard chars
        $number_string = $numbers[0][0] ?? '';
        $sql = <<<'SQL'
            select postal_code, latitude, longitude, displayed_city from postal_codes_germany
            where ((? != '') and (postal_code like ? || '%'))
            or ((? != '') and (displayed_city ilike ? || '%'))
            or ((? != '') and (displayed_city ilike '% ' || ? || '%'))
            or similarity(displayed_city, ?) > 0.5
            order by postal_code like ? || '%' desc,
            displayed_city ilike ? || '%' desc,
            similarity(displayed_city, ?) desc,
            postal_code asc
            limit 10;
        SQL;
        $stmt = DB::getPdo()->prepare($sql);
        $stmt->execute([ $number_string, $number_string, $text_string, $text_string, $text_string, $text_string, $text_string, $number_string, $text_string, $text_string]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function normalize(string $unicode_string) {
        $transliterator = Transliterator::create("Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove; ");
        $transliterated = $transliterator->transliterate($unicode_string);
        $without_punctuation = preg_replace('/[[:punct:]]/', ' ', $transliterated);
        $words = preg_split('/\s+/', $without_punctuation, -1, PREG_SPLIT_NO_EMPTY);
        return $words;
    }
}