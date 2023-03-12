<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Tests\Service;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use GuzzleHttp\Exception\GuzzleException;
use Paywa\CommissionTask\Enum\Currency;
use Paywa\CommissionTask\Service\ExchangeRateService;

class ExchangeRatesTest extends TestCase
{
    private ExchangeRateService $exchangeRatesService;

    /**
     * @return void
     * @throws GuzzleException
     */
    public function setUp(): void
    {
        $this->exchangeRatesService = ExchangeRateService::getInstance();

        $reflection = new ReflectionClass($this->exchangeRatesService);
        $reflectionProperty = $reflection->getProperty('exchangeRates');

        $reflectionProperty->setValue(
            $this->exchangeRatesService,
            [
                'USD' => 1.1497,
                'JPY' => 129.53,
            ],
        );
    }

    /**
     * @param BigDecimal $amount
     * @param Currency $convertFromCurrency
     * @param BigDecimal $expectation
     * @throws MathException
     *
     * @dataProvider dataProviderForConvertToEuro
     */
    public function testConvertToEuro(
        BigDecimal $amount,
        Currency $convertFromCurrency,
        BigDecimal $expectation,
    ) {
        $this->assertEquals(
            $expectation,
            $this->exchangeRatesService->convertToEuro(
                $amount,
                $convertFromCurrency,
            ),
        );
    }

    /**
     * @throws MathException
     */
    public static function dataProviderForConvertToEuro(): array
    {
        return [
            'from 100 USD' => [
                BigDecimal::of(100),
                Currency::USD,
                BigDecimal::of(86.98),
            ],
        ];
    }
}
