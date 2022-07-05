<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 3, unique: true)]
    private string $currency_identifier;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $currency_icon = null;

    #[ORM\OneToMany(mappedBy: 'currencyFrom', targetEntity: CurrencyRate::class, orphanRemoval: true)]
    private Collection $currencyRatesTo;

    #[ORM\OneToMany(mappedBy: 'currencyTo', targetEntity: CurrencyRate::class, orphanRemoval: true)]
    private Collection $currencyRatesFrom;

    public function __construct()
    {
        $this->currencyRatesTo = new ArrayCollection();
        $this->currencyRatesFrom = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrencyIdentifier(): string
    {
        return $this->currency_identifier;
    }

    public function setCurrencyIdentifier(string $currency_identifier): self
    {
        $this->currency_identifier = $currency_identifier;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCurrencyIcon(): ?string
    {
        return $this->currency_icon;
    }

    public function setCurrencyIcon(string $currency_icon): self
    {
        $this->currency_icon = $currency_icon;

        return $this;
    }

    /**
     * @return Collection<int, CurrencyRate>
     */
    public function getCurrencyRatesTo(): Collection
    {
        return $this->currencyRatesTo;
    }

    public function addCurrencyRatesTo(CurrencyRate $currencyRatesTo): self
    {
        if (!$this->currencyRatesTo->contains($currencyRatesTo)) {
            $this->currencyRatesTo[] = $currencyRatesTo;
            $currencyRatesTo->setCurrencyFrom($this);
        }

        return $this;
    }

    public function removeCurrencyRatesTo(CurrencyRate $currencyRatesTo): self
    {
        if ($this->currencyRatesTo->removeElement($currencyRatesTo)) {
            // set the owning side to null (unless already changed)
            if ($currencyRatesTo->getCurrencyFrom() === $this) {
                $currencyRatesTo->setCurrencyFrom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CurrencyRate>
     */
    public function getCurrencyRatesFrom(): Collection
    {
        return $this->currencyRatesFrom;
    }

    public function addCurrencyRatesFrom(CurrencyRate $currencyRatesFrom): self
    {
        if (!$this->currencyRatesFrom->contains($currencyRatesFrom)) {
            $this->currencyRatesFrom[] = $currencyRatesFrom;
            $currencyRatesFrom->setCurrencyTo($this);
        }

        return $this;
    }

    public function removeCurrencyRatesFrom(CurrencyRate $currencyRatesFrom): self
    {
        if ($this->currencyRatesFrom->removeElement($currencyRatesFrom)) {
            // set the owning side to null (unless already changed)
            if ($currencyRatesFrom->getCurrencyTo() === $this) {
                $currencyRatesFrom->setCurrencyTo(null);
            }
        }

        return $this;
    }
}
