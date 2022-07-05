<?php

namespace App\Entity;

use App\Repository\CurrencyRateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
class CurrencyRate
{
    #[ORM\Column(type: 'decimal', precision: 10, scale: 4)]
    private string $rate;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'currencyRatesTo')]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currencyFrom;

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
