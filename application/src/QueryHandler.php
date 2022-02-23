<?php

namespace src;

class QueryHandler
{
    public function handleGet(array $request): string
    {
        $result = '';
        foreach ($request as $key=>$value) {
            $result .= sprintf('%s=%s&', $key, $value);
        }
        if (preg_match('~(.*freeDrive=.*)\?~', $result, $matches)) {
            return $matches[1];
        }
        return rtrim($result, ' &');
    }

    public function handlePost(array $request)
    {
        $order = new Order($request);
        return $order->toString();
    }
}
