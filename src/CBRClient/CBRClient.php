<?php

namespace cbInformer\CBRClient;

use cbInformer\Singletone\Cache;
use DateTime;
use Exception;
use SimpleXMLElement;
use SoapClient;
use SoapFault;

class CBRClient
{
    public const CBR_DATE_FORMAT = 'Y-m-d';
    private string $wsdlEndpoint = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL';
    private SoapClient $client;

    /**
     * CBRClient constructor.
     * @throws SoapFault
     */
    public function __construct()
    {
        $this->client = new SoapClient($this->wsdlEndpoint, ['cache' => WSDL_CACHE_DISK]);
    }

    /**
     * @throws Exception
     */
    public function getDynamicRate(string $fromDate, string $toDate, string $currencyCode)
    {
        if (DateTime::createFromFormat(self::CBR_DATE_FORMAT, $toDate) !== false) {
            $cacheKey = $fromDate . $toDate . $currencyCode;
            $response = Cache::getInstance()->get($cacheKey);
            if (!$response) {
                try {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $response = $this->client->GetCursDynamicXML([
                        'FromDate' => (string)$fromDate,
                        'ToDate' => (string)$toDate,
                        'ValutaCode' => (string)$currencyCode
                    ]);
                    $rates = new SimpleXMLElement($response->GetCursDynamicXMLResult->any);
                    $response = [];
                    foreach ($rates->ValuteCursDynamic as $rate) {
                        $response[] = (float)$rate->Vcurs[0];
                    }
                    Cache::getInstance()->set($cacheKey, $response);
                } catch (Exception $exception) {
                    echo($exception->getMessage());
                    return false;
                }
            }
            return $response;
        }
        return false;
    }
}
