<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 19:16
 */

namespace Errogaht\DeliveryAggregator\Carrier\Pec;


class TermParser
{
    /**
     * [1, 2]
     * @param $maybeArray
     * @return array
     */
    public static function parseMaybeArrayOfTerms($maybeArray)
    {
        $res = [];
        if (is_array($maybeArray)) {
            foreach ($maybeArray as $item) {
                $res[] = self::parseStringTerm($item);
            }
        } elseif (is_string($maybeArray)) {
            $res[] = self::parseStringTerm($maybeArray);
        }

        if (empty($res)) {
            return [null, null];
        }
        $fromArr = [];
        $toArr = [];
        foreach ($res as list($from, $to)) {
            $fromArr[] = $from;
            $toArr[] = $to;
        }
        return [min($fromArr), max($toArr)];
    }

    /**
     * "1" => [1, 1]
     * "1 - 2" => [1, 2]
     * @return array
     */
    private static function parseStringTerm($string)
    {
        $r = explode(' - ', $string);

        if (count($r) === 1) {
            return [(int)$r[0], (int)$r[0]];
        }

        if (count($r) === 2) {
            return [(int)$r[0], (int)$r[1]];
        }

        return [null, null];
    }
}