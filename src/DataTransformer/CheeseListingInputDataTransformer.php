<?php


namespace App\DataTransformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CheeseListingInput;
use App\Entity\CheeseListing;

class CheeseListingInputDataTransformer implements DataTransformerInterface
{

    /**
     * @param CheeseListingInput $input
     */
    public function transform($input, string $to, array $context = [])
    {
        dump($input,$to,$context);
        return CheeseListing();
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if($data instanceof CheeseListing){
            //already transform
            return false;
        }
        return $to === CheeseListing::class && ($context['input']['class'] ?? null === CheeseListingInput::class);
    }
}