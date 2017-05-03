<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Slugify
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait Slugify
{
    /**
     * Generates slug from string
     *
     * @param  String  $string                       Source string
     * @param  Boolean $removeWesternEuropeanAccents [optional] When true, removes western european accents (ex. é => e) - default: true
     * @param  String  $separator                    [optional] Character to use to replace non-alphanum chars - default: '-'
     *
     * @return String  clean string
     */
    public function slugify($string, $removeWesternEuropeanAccents = true, $separator = '-')
    {
        $slug = trim($string);
        if ($removeWesternEuropeanAccents) {
            // That may seem weird but this seems to be the better way to do it
            $slug = htmlentities($slug, ENT_NOQUOTES, 'UTF-8');
            $slug = preg_replace('/&#?([a-zA-Z])[a-zA-Z0-9]*;/i', '${1}', $slug);
        }
        // Convert string
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        // Remove illegal characters
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = mb_strtolower(trim($slug, $separator), 'UTF-8');
        // Replace illegal characters
        $slug = preg_replace("/[\/_|+ -]+/", $separator, $slug);

        return $slug;
    }
}
