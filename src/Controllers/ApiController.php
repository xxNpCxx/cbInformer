<?php

/**
 * Задание:
 * получение курсов, кроскурсов ЦБ.
 * требование:
 * - на входе: дата, код валюты, код базовой валюты (по-умолчанию RUR);
 * - получать курсы с cbr.ru;
 * - на выходе: значение курса и разница с предыдущим торговым днем;
 * - кешировать данные cbr.ru.
 */

namespace cbInformer\Controllers;

use cbInformer\CBRClient\CBRClient;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends BaseController
{
    private const  DEFAULT_CURRENCY_CODE = 'R01015';
    private const  MAX_DAYS_RANGE = '7';

    private CBRClient $CBRClient;
    private int $responceStatus = Response::HTTP_OK;

    public function __construct()
    {
        parent::__construct();
        $this->CBRClient = new CBRClient();
    }

    /**
     * @throws Exception
     */
    public function getCurrencyRateDiff()
    {
        $code = $this->request->request->get('code') ?? self::DEFAULT_CURRENCY_CODE;
        $date = $this->request->request->get('date')
            ? date(CBRClient::CBR_DATE_FORMAT, strtotime($this->request->request->get('date')))
            : date(CBRClient::CBR_DATE_FORMAT);
        $prevDayDate = date(CBRClient::CBR_DATE_FORMAT, strtotime($date . '- ' . self::MAX_DAYS_RANGE . ' day'));
        $rates = $this->CBRClient->getDynamicRate($prevDayDate, $date, $code);
        if ($rates) {
            $fromDayRate = $rates[0];
            $toDayRate = $rates[1];
            $diff = $fromDayRate - $toDayRate;
            $response = [
                'data' => [
                    'currentRate' => $toDayRate,
                    'diffToPrevDay' => $diff
                ]
            ];
        } else {
            $response = ['error' => "Cannnot find currency: $code"];
            $this->responceStatus = Response::HTTP_BAD_REQUEST;
        }
        echo(new JsonResponse($response, $this->responceStatus, [], false));
    }


    /**
     * @throws Exception
     */
    public function getCrossCurrencyRateDiff()
    {
        $date = $this->request->request->get('date')
            ? date(CBRClient::CBR_DATE_FORMAT, strtotime($this->request->request->get('date')))
            : date(CBRClient::CBR_DATE_FORMAT);
        $baseCode = $this->request->request->get('baseCode', self::DEFAULT_CURRENCY_CODE);
        $quoteCode = $this->request->request->get('quoteCode', null);

        $prevDayDate = date(CBRClient::CBR_DATE_FORMAT, strtotime($date . '- ' . self::MAX_DAYS_RANGE . ' day'));

        $baseRates = $this->CBRClient->getDynamicRate($prevDayDate, $date, $baseCode);
        $quoteRates = $this->CBRClient->getDynamicRate($prevDayDate, $date, $quoteCode);

        if ($baseRates && $quoteRates) {
            $prevCrossRate = (float)$baseRates[0] / (float)$quoteRates[0];
            $crossRate = (float)$baseRates[1] / (float)$quoteRates[1];

            $response = [
                'data' => [
                    'currentCrossRate' => $crossRate,
                    'diffToPrevDayCrossRate' => number_format($crossRate - $prevCrossRate, 8),
                ]
            ];
        } else {
            $this->responceStatus = Response::HTTP_BAD_REQUEST;
            $response = ['error' => "Cannnot find currency"];
        }
        echo(new JsonResponse($response, $this->responceStatus));


    }


}
