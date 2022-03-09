<?php


namespace App\ApiPlatform;



use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DailyStatsDateFilter implements FilterInterface
{
    public const FROM_FILTER_CONTEXT = 'daily_stats_from';

    private $throwOnInvailid;
    private $logger;
    public function __construct(bool $throwOnInvalid = false,LoggerInterface $logger){
        $this->throwOnInvailid = $throwOnInvalid;
        $this->logger = $logger;
    }

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $from = $request->query->get('from');
        if(!$from){
            return;
        }

        $fromDate = \DateTimeImmutable::createFromFormat('Y-m-d',$from);

        if(!$fromDate && $this->throwOnInvailid){
            throw new BadRequestHttpException("Invailid from date format.");
        }

        if($fromDate){
            $this->logger->info(sprintf('Filtering from date "%s"', $from));
            $fromDate = $fromDate->setTime(0,0,0);
            $context[self::FROM_FILTER_CONTEXT] = $fromDate;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            "from" => [
                "property" => null,
                "type" => "string",
                "required" => false,
                "openapi" => [
                    "description" => "From date e.g 2020-09-01."
                ],
            ]
        ];
    }
}