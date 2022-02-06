<?php

namespace src;

class QueryHandler
{
    public function handleGet(array $get): string
    {
        $result = '';
        foreach ($get as $key=>$item) {
            if (!preg_match('~\d~', $key)) {
                $result .= "$key=$item&";
            }
        }
        return rtrim($result, ' &');
    }
}
