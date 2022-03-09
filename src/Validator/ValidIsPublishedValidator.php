<?php

namespace App\Validator;

use App\Entity\CheeseListing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidIsPublishedValidator extends ConstraintValidator
{
    private $entityManager;
    private $security;
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\ValidIsPublished */
        if (!$value instanceof CheeseListing) {
            throw new \LogicException('Only CheeseListing is supported');
        }

        $originalData = $this->entityManager
            ->getUnitOfWork()
            ->getOriginalEntityData($value);
        //dd($originalData);

        $previousIsPublished = ($originalData['isPublished'] ?? false);
        if ($previousIsPublished === $value->getIsPublished()) {
            // isPublished didn't change!
            return;
        }

        if($value->getIsPublished()){
            //we are publishing

            //DON'T ALLOW short  description, unlessyou are an admin
            if(strlen($value->getDescription() < 100) && !$this->security->isGranted('ROLE_ADMIN')){
                $this->context->buildViolation($constraint->message)
                ->atPath('description')
                ->addViolation();
            }
            return;
        }

        //WE are unpublishing

        if(!$this->security->isGranted("ROLE_ADMIN")){
            throw new AccessDeniedException("Only user can unpublish.");
            $this->context->buildViolation("Only user can unpublish")
                ->addViolation();
        }

    }
}
