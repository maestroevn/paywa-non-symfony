<?php

namespace Paywa\CommissionTask;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Paywa\CommissionTask\Enum\UserType;
use Paywa\CommissionTask\Entity\UserOperation;
use Paywa\CommissionTask\Repository\UserOperationRepository;
use Paywa\CommissionTask\Service\CommissionCalculator\BusinessCommissionCalculatorService;
use Paywa\CommissionTask\Service\CommissionCalculator\PrivateCommissionCalculatorService;

class App
{
    /**
     * @param string $inputFilePath
     * @return string
     * @throws GuzzleException
     * @throws Exception
     */
    public function run(string $inputFilePath): string
    {
        if (false === is_readable($inputFilePath)) {
            throw new Exception(
                sprintf(
                    ' Input file in `%s` is not readable',
                    $inputFilePath,
                ),
            );
        }

        $fileOpenHandle = fopen($inputFilePath, 'r');
        if (false === $fileOpenHandle) {
            throw new Exception(
                sprintf(
                    ' Input file in `%s` is not readable',
                    $inputFilePath,
                ),
            );
        }

        $output = '';

        while (false !== ($data = fgetcsv($fileOpenHandle))) {
            $operation = new UserOperation($data);

            /** @var UserOperationRepository $userOperationRepository */
            $userOperationRepository = UserOperationRepository::getInstance();
            $userOperationRepository->addOperation($operation);

            $commissionCalculator = match ($operation->getUserType()) {
                UserType::PRIVATE => new PrivateCommissionCalculatorService(),
                UserType::BUSINESS => new BusinessCommissionCalculatorService(),
            };
            $commission = $commissionCalculator->calculateCommission($operation);

            $output .= $commission . PHP_EOL;
        }

        fclose($fileOpenHandle);

        return $output;
    }
}
