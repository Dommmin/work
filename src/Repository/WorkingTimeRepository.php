<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\WorkingTime;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkingTime>
 */
class WorkingTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkingTime::class);
    }

    //    /**
    //     * @return WorkingTime[] Returns an array of WorkingTime objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WorkingTime
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function create(array $data): WorkingTime
    {
        $workingTime = new WorkingTime();
        $workingTime->setEmployee($data['employee'] ?? null);
        $workingTime->setStartDate($data['startDate'] ?? null);
        $workingTime->setEndDate($data['endDate'] ?? null);

        return $workingTime;
    }

    public function getDaySummaryForEmployee(Employee $employee, string $date): mixed
    {
        return $this->createQueryBuilder('w')
            ->where('w.employee = :employee')
            ->setParameter('employee', $employee)
            ->andWhere('w.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMonthSummaryForEmployee(Employee $employee, string $date): mixed
    {
        $startOfMonth = Carbon::createFromFormat('Y-m', $date)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        return $this->createQueryBuilder('w')
            ->where('w.employee = :employee')
            ->setParameter('employee', $employee)
            ->andWhere('w.date BETWEEN :startOfMonth AND :endOfMonth')
            ->setParameter('startOfMonth', $startOfMonth)
            ->setParameter('endOfMonth', $endOfMonth)
            ->getQuery()
            ->getResult();
    }
}
