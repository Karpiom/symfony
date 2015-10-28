<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="task")
 */
class Task
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
	 * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="text")
     * @Assert\NotBlank()
	 */
	protected $description;

	/**
	 * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
	 */
	protected $endTime;
	
	/**
	 * @ORM\Column(type="boolean", options={"default":0})
	 */
	protected $ended;
	
	/**
	 * @ORM\Column(type="smallint", options={"default":0})
     * @Assert\NotBlank()
	 */
	protected $priority;
	
	/**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="tasksList")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    protected $category;
    
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
     * @return Task
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
     * Set description
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set endTime
     *
     * @param \datetime $endTime
     *
     * @return Task
     */
    public function setEndTime(\DateTime $endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \datatime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set ended
     *
     * @param boolean $ended
     *
     * @return Task
     */
    public function setEnded($ended)
    {
        $this->ended = $ended;

        return $this;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return Task
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get ended
     *
     * @return boolean
     */
    public function getEnded()
    {
        return $this->ended;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Task
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
