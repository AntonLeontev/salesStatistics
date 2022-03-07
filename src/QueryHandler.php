<?php

namespace src;

class QueryHandler
{
    /**
     * Gathers GET params into string
     *
     * @param array $request Input params array
     * @return string String like "key1=value1&key2=value2"
     */
    public function handleQuery(array $request): string
    {
        $result = '';
        foreach ($request as $key=>$value) {
            $result .= sprintf('%s=%s&', $key, $value);
        }
        if (preg_match('~(.*)\?~', $result, $matches)) {
            return $matches[1];
        }
        return rtrim($result, ' &');
    }

    /**
     * Explodes request into associative array with parsed
     * data from query params
     *
     * @param string $request Input request
     * @return array
     */
    public function parseQuery(string $request): array
    {
        parse_str($request, $parsedData);
        return $parsedData;
    }
}
