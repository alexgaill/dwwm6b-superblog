<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

// #[ORM\Entity]
class Category2 {

    #[
        ORM\Column(type: "integer"),
        ORM\Id(),
        ORM\GeneratedValue()
    ]
    private int $id;

    #[ORM\Column(type: "string", length: 65)]
    private string $name;

    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}