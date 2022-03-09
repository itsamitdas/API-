<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\ApiPlatform\DailyStatsDateFilter;
use App\Entity\DailyStats;
use App\Repository\CheeseListingRepository;
use App\Service\StatsHelper;



class DailyStatsProvider implements ContextAwareCollectionDataProviderInterface,ItemDataProviderInterface, RestrictedDataProviderInterface {

    private $cheeseListingRepository;
    private $statsHelper;
    private $pagination;
    public function __construct(CheeseListingRepository $cheeseListingRepository, StatsHelper $statsHelper, Pagination $pagination)
    {
        $this->cheeseListingRepository = $cheeseListingRepository;
        $this->statsHelper = $statsHelper;
        $this->pagination = $pagination;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {

        list($page, $offset, $limit) = $this->pagination->getPagination($resourceClass, $operationName);

        $papaginator =  new DailyStatsPaginator(
            $this->statsHelper,
            $page,$limit
        );

        $fromDate = $context[DailyStatsDateFilter::FROM_FILTER_CONTEXT] ?? null;
        if($fromDate) {
            $papaginator->setFromDate($fromDate);
        }
        return $papaginator;
    }
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = [])
    {
        return $this->statsHelper->fetchOne($id);   
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }

}
?>