<?php
// app/Traits/FullTextSearch.php

namespace App\Http\Traits;

trait FullTextSearch
{

        /**
     * Replaces spaces with full text search wildcards
     *
     * @param string $term
     *
     * @return string
     */
    protected function fullTextWildcards( $term ) {
        // removing symbols used by MySQL
        $reservedSymbols = [ '-', '+', '<', '>', '@', '(', ')', '~' ];
        $term            = str_replace( $reservedSymbols, '', $term );

        $words = explode( ' ', $term );

        foreach ( $words as $key => $word ) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if ( strlen( $word ) >= 3 ) {
                $words[ $key ] = '+' . $word . '*';
            }
        }

        $searchTerm = implode( ' ', $words );

        return $searchTerm;
    }

    public function scopeSearch($query, $term)
    {

            if (strlen($term) > 50) return $query;
            $columns = implode( ',', $this->searchable );
            if ( $term ) {
                $query->whereRaw( "MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->fullTextWildcards( $term ) );
            } 
    
            return $query;
        


        // if ($search) {
        //     return $query->whereRaw("MATCH(name, description) AGAINST(? IN BOOLEAN MODE)", [$search]);
        // }

        // return $query;
    }
}
