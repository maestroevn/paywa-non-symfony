<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Service;

use Exception;
use GuzzleHttp\Utils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Math\Exception\MathException;
use Paywa\CommissionTask\Enum\Currency;
use Paywa\CommissionTask\CommonCore\Singleton;

class ExchangeRateService extends Singleton
{
    private const ENDPOINT = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    private array $exchangeRates = [];

    /**
     * @return array|null
     * @throws GuzzleException
     */
    protected static function fetchExchangeRates(): ?array
    {
        $httpClient = new Client();
        $response = $httpClient->get(self::ENDPOINT);

        $responseArray = Utils::jsonDecode($response->getBody()->getContents(), true);

        if (
            false === is_array($responseArray)
            || false === array_key_exists('rates', $responseArray)
            || false === is_array($responseArray['rates'])
        ) {
            return null;
        }

        return $responseArray['rates'];
    }

    /**
     * @return ExchangeRateService
     * @throws GuzzleException
     * @throws Exception
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public static function getInstance(): ExchangeRateService
    {
        $className = static::class;
        if (false === array_key_exists($className, self::$instances)) {
            self::$instances[$className] = new static();

            $exchangeRates = self::fetchExchangeRates();
            if (is_null($exchangeRates)) {
                throw new Exception('Invalid response for currency exchange rates');
            }
            self::$instances[$className]->exchangeRates = $exchangeRates;

            foreach (Currency::cases() as $currency) {
                if (false === array_key_exists($currency->value, self::$instances[$className]->exchangeRates)) {
                    throw new Exception(
                        sprintf(
                            'Exchange rate for `%s` currency is missing.',
                            $currency->value,
                        ),
                    );
                }
            }
        }

        return self::$instances[$className];
    }

    /**
     * @param BigDecimal $amount
     * @param Currency $convertFromCurrency
     * @return BigDecimal
     * @throws MathException
     */
    public function convertToEuro(BigDecimal $amount, Currency $convertFromCurrency): BigDecimal
    {
        /** @var float $convertFromCurrencyRate */
        $convertFromCurrencyRate = $this->exchangeRates[$convertFromCurrency->value];

        return $amount->dividedBy(
            $convertFromCurrencyRate,
            $convertFromCurrency->decimalPlaces(),
            RoundingMode::UP,
        );
    }

    /**
     * @param BigDecimal $amount
     * @param Currency $convertToCurrency
     * @return BigDecimal
     * @throws MathException
     */
    public function convertFromEuro(BigDecimal $amount, Currency $convertToCurrency): BigDecimal
    {
        /** @var float $convertToCurrencyRate */
        $convertToCurrencyRate = $this->exchangeRates[$convertToCurrency->value];

        return $amount->multipliedBy($convertToCurrencyRate);
    }
}
