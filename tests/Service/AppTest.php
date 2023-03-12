<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Tests\Service;

use ReflectionClass;
use ReflectionException;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Exception\GuzzleException;
use Paywa\CommissionTask\App;
use Paywa\CommissionTask\Service\ExchangeRateService;

class AppTest extends TestCase
{
    /**
     * @return void
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function setUp(): void
    {
        $exchangeRatesService = ExchangeRateService::getInstance();

        $reflection = new ReflectionClass($exchangeRatesService);
        $reflectionProperty = $reflection->getProperty('exchangeRates');

        $reflectionProperty->setValue(
            $exchangeRatesService,
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
