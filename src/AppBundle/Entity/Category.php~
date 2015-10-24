<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $name;

	/**
	 * @ORM\OneToMany(targetEntity="Task", mappedBy="category", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
	 */
    protected $tasksList;
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasksList = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add tasksList
     *
     * @param \AppBundle\Entity\Task $tasksList
     *
     * @return Category
     */
    public function addTasksList(\AppBundle\Entity\Task $tasksList)
    {
        $this->tasksList[] = $tasksList;

        return $this;
    }

    /**
     * Remove tasksList
     *
     * @param \AppBundle\Entity\Task $tasksList
     */
    public function removeTasksList(\AppBundle\Entity\Task $tasksList)
    {
        $this->tasksList->removeElement($tasksList);
    }

    /**
     * Get tasksList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasksList()
    {
        return $this->tasksList;
    }
}
