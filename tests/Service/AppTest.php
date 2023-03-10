<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Tests\Service;

use Paywa\CommissionTask\App;
use ReflectionClass;
use PHPUnit\Framework\TestCase;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use GuzzleHttp\Exception\GuzzleException;
use Paywa\CommissionTask\Enum\Currency;
use Paywa\CommissionTask\Service\ExchangeRates;

class AppTest extends TestCase
{
    private ExchangeRates $exchangeRatesService;

    /**
     * @return void
     * @throws GuzzleException
     */
    public function setUp(): void
    {
        $this->exchangeRatesService = ExchangeRates::getInstance();

        $reflection = new ReflectionClass($this->exchangeRatesService);
        $reflectionProperty = $reflection->getProperty('exchangeRates');

        $reflectionProperty->setValue(
            $this->exchangeRatesService,
            [
                'USD' => 1.1497,
                'JPY' => 129.53,
                'EUR' => 1,
            ],
        );
    }

    /**
     * @param string $inputFilePath
     * @param string $outputFilePath
     * @return void
     * @throws GuzzleException
     *
     * @dataProvider dataProviderForRun
     */
    public function testRun(
        string $inputFilePath,
        string $outputFilePath,
    ): void {
        $app = new App();

        $this->assertSame(
            file_get_contents($outputFilePath),
            $app->run($inputFilePath),
        );
    }

    /**
     * @return array[]
     */
    public static function dataProviderForRun(): array
    {
        return [
            'test1' => [
                './data/input.csv',
                './data/output.csv',
            ],
        ];
    }
}
