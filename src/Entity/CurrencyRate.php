<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CurrencyRateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
class CurrencyRate
{
    #[Assert\Length(max:10)]
    #[ORM\Column(type: 'decimal', precision: 10, scale: 4)]
    private string $rate;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'currencyRatesTo')]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currencyFrom;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'currencyRatesFrom')]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currencyTo;

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCurrencyFrom(): Currency
    {
        return $this->currencyFrom;
    }

    public function setCurrencyFrom(Currency $currencyFrom): self
    {
        $this->currencyFrom = $currencyFrom;

        return $this;
    }

    public function getCurrencyTo(): Currency
    {
        return $this->currencyTo;
    }

    public function setCurrencyTo(Currency $currencyTo): self
    {
        $this->currencyTo = $currencyTo;

        return $this;
    }
}
