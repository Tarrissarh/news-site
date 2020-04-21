<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", name="pub_date")
     */
    private $pubDate;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $category = [];

    /**
     * @ORM\Column(type="boolean", name="is_year")
     */
    private $isYear = false;

    /**
     * @ORM\Column(type="boolean", name="is_month")
     */
    private $isMonth = false;

    /**
     * @ORM\Column(type="boolean", name="is_week")
     */
    private $isWeek = false;

    /**
     * @ORM\Column(type="boolean", name="is_daily")
     */
    private $isDaily = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

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

    public function getPubDate(): ?\DateTimeInterface
    {
        return $this->pubDate;
    }

    public function setPubDate(\DateTimeInterface $pubDate): self
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    public function getCategory(): ?array
    {
        return $this->category;
    }

    public function setCategory(?array $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getIsMonth(): ?bool
    {
        return $this->isMonth;
    }

    public function setIsMonth(bool $isMonth): self
    {
        $this->isMonth = $isMonth;

        return $this;
    }

    public function getIsWeek(): ?bool
    {
        return $this->isWeek;
    }

    public function setIsWeek(bool $isWeek): self
    {
        $this->isWeek = $isWeek;

        return $this;
    }

    public function getIsDaily(): ?bool
    {
        return $this->isDaily;
    }

    public function setIsDaily(bool $isDaily): self
    {
        $this->isDaily = $isDaily;

        return $this;
    }

    public function getIsYear(): ?bool
    {
        return $this->isYear;
    }

    public function setIsYear(bool $isYear): self
    {
        $this->isYear = $isYear;

        return $this;
    }
}
