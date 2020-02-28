<?php

/**
 * Задание:
получение курсов, кроскурсов ЦБ.
требование:
- на входе: дата, код валюты, код базовой валюты (по-умолчанию RUR);
- получать курсы с cbr.ru;
- на выходе: значение курса и разница с предыдущим торговым днем;
- кешировать данные cbr.ru.
 */
namespace cbInformer\Controllers;

use ExchangeRatesCBRF;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController
{
    protected Request $request;
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }
}
