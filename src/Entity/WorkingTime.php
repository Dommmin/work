<?php

namespace App\Entity;

use App\Repository\WorkingTimeRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkingTimeRepository::class)]
class WorkingTime
{
    CONST int HOURS_PER_DAY = 12;
    CONST int HOURS_PER_MONTH = 40;
    CONST int SALARY_PER_HOUR = 20;
    CONST int PERCENTAGE_SALARY_FOR_OVERTIME = 200 / 100;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'workingTimes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Please select an employee.')]
    private ?Employee $employee = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: 'Please enter a start date.')]
    private ?DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: 'Please enter an end date.')]
    private ?DateTimeInterface $endDate = null;

    #[ORM\Column(type: 'date')]
    private ?DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
