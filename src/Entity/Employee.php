<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true)]
    private string $uuid;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Assert\NotNull(message: 'Please enter your first name.')]
    #[Assert\Length(min: 2, max: 64)]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Assert\NotNull(message: 'Please enter your last name.')]
    #[Assert\Length(min: 2, max: 64)]
    private ?string $lastName = null;

    /**
     * @var Collection<int, WorkingTime>
     */
    #[ORM\OneToMany(targetEntity: WorkingTime::class, mappedBy: 'employee', cascade: ['persist'])]
    private Collection $workingTimes;

    public function __construct()
    {
        $this->uuid = Uuid::uuid7()->toString();
        $this->workingTimes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return Collection<int, WorkingTime>
     */
    public function getWorkingTimes(): Collection
    {
        return $this->workingTimes;
    }

    public function addWorkingTime(WorkingTime $workingTime): static
    {
        if (!$this->workingTimes->contains($workingTime)) {
            $this->workingTimes->add($workingTime);
            $workingTime->setEmployee($this);
        }

        return $this;
    }

    public function removeWorkingTime(WorkingTime $workingTime): static
    {
        if ($this->workingTimes->removeElement($workingTime)) {
            // set the owning side to null (unless already changed)
            if ($workingTime->getEmployee() === $this) {
                $workingTime->setEmployee(null);
            }
        }

        return $this;
    }
}
