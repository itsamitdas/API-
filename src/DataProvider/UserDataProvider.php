<?php
namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface , RestrictedDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface{
    
    private $collectionDataProvider;
    private $security;
    private $itemDataProvider;
    private $logger;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider,LoggerInterface $logger, ItemDataProviderInterface $itemDataProvider, Security $security)
    {
        $this->collectionDataProvider = $collectionDataProvider;        
        $this->itemDataProvider = $itemDataProvider;
        $this->security = $security;
        $this->logger = $logger;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        
        /** @var User|null $item */
        $item = $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);
        
        if (!$item) {
            return null;
        }

        //Handle in listner
        //$item->setIsMe(true);
        
        return $item;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        
        /** @var User[] $users */
        $users = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        $currentUser = $this->security->getUser();
        
        foreach($users as $user){
            //Handle in listner
            //$user->setIsMe($currentUser === $user);
        }
        return $users;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {       
        return $resourceClass === User::class;
    }
}
?>