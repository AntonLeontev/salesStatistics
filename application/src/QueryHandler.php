<?php

namespace src;

class QueryHandler
{
    public function handleGet(array $request): string
    {
        $order = new Order($request);
        return $order->toString();
    }
}
