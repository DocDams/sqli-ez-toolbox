<?php

namespace SQLI\EzToolboxBundle\Entity\Doctrine;

use SQLI\EzToolboxBundle\Repository\Doctrine\GroupMailRepository;
use Doctrine\ORM\Mapping as ORM;
use SQLI\EzToolboxBundle\Annotations\Annotation as SQLIToolbox;

/**
 * @ORM\Entity(repositoryClass=GroupMailRepository::class)
 * @ORM\Table(name="ceva_group_mail")
 * @SQLIToolbox\Entity(update=true,create=true,delete=true,description="Ceva Group Mail",tabname="default")
 */
class GroupMail
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SQLIToolbox\EntityProperty(readonly=true)
     */
    private int $id;

    /**
     * @var bool
     * @ORM\Column(name="is_corporate", type="boolean")
     * @SQLIToolbox\EntityProperty(description="Is Corporate ?")
     * */
    private bool $isCorporate;

    /**
     * @var string
     * @ORM\Column(name="group_name", type="string", nullable=false)
     * @SQLIToolbox\EntityProperty(description="Group Name")
     */
    private ?string $groupName = null;

    /**
     * @var string|null
     * @ORM\Column(name="group_email", type="string", length=128)
     * @SQLIToolbox\EntityProperty(description="Group Email")
     */
    private ?string $groupEmail = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isCorporate(): bool
    {
        return $this->isCorporate;
    }

    /**
     * @param bool $isCorporate
     */
    public function setIsCorporate(bool $isCorporate): void
    {
        $this->isCorporate = $isCorporate;
    }

    /**
     * @return string
     */
    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName(?string $groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return string
     */
    public function getGroupEmail(): ?string
    {
        return $this->groupEmail;
    }

    /**
     * @param string $groupEmail
     */
    public function setGroupEmail(?string $groupEmail): void
    {
        $this->groupEmail = $groupEmail;
    }
}