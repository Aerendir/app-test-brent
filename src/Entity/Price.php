<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use App\Serializer\ApiGroups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        #[Groups([ApiGroups::ALWAYS])]
        private readonly \DateTimeImmutable $date,

        #[ORM\Column(type: 'money')]
        #[Groups([ApiGroups::ALWAYS])]
        private readonly Money $price,

        #[ORM\Column(type: Types::STRING)]
        private readonly string $source,
    ){}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate() : \DateTimeImmutable
    {
        return $this->date;
    }

    public function getPrice() : Money
    {
        return $this->price;
    }

    public function getSource() : string
    {
        return $this->source;
    }
}
