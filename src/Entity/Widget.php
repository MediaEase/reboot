<?php

declare(strict_types=1);

/*
 * This file is part of the MediaEase project.
 *
 * (c) Thomas Chauveau <contact.tomc@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\WidgetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: WidgetRepository::class)]
#[ORM\Table(name: '`widget`')]
#[OA\Schema(description: 'Widget entity representing a widget in the system.')]
class Widget
{
    public const GROUP_GET_WIDGET = 'get_widget';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS])]
    #[OA\Property(description: 'The unique identifier of the widget.', format: 'int')]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_WIDGET])]
    #[OA\Property(description: 'The name of the widget.', maxLength: 20)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_WIDGET])]
    #[OA\Property(description: 'The alternative name of the widget.', maxLength: 20)]
    private ?string $altName = null;

    #[ORM\Column(length: 255)]
    #[Groups([User::GROUP_GET_USER, User::GROUP_GET_USERS, self::GROUP_GET_WIDGET])]
    #[OA\Property(description: 'The type of the widget.', maxLength: 255)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAltName(): ?string
    {
        return $this->altName;
    }

    public function setAltName(string $altName): static
    {
        $this->altName = $altName;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
