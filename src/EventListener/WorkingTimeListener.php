<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\WorkingTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class WorkingTimeListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if (!$object instanceof WorkingTime) {
            return;
        }

        $this->updateDateFromStartDate($object);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if (!$object instanceof WorkingTime) {
            return;
        }

        $this->updateDateFromStartDate($object);
    }

    private function updateDateFromStartDate(WorkingTime $workingTime): void
    {
        $workingTime->setDate($workingTime->getStartDate());
    }
}
