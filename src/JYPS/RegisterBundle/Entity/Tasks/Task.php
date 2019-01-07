<?php

namespace JYPS\RegisterBundle\Entity\Tasks;

/**
 * Task
 */
class Task
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $type;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime|null
     */
    private $processStartTime;

    /**
     * @var \DateTime|null
     */
    private $processEndTime;

    /**
     * @var int
     */
    private $queue;

    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $targetId;

    /**
     * @var int
     */
    private $status;

    /**
     * @var json|null
     */
    private $params;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type.
     *
     * @param int $type
     *
     * @return Task
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createTime.
     *
     * @param \DateTime $createTime
     *
     * @return Task
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime.
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set processStartTime.
     *
     * @param \DateTime|null $processStartTime
     *
     * @return Task
     */
    public function setProcessStartTime($processStartTime = null)
    {
        $this->processStartTime = $processStartTime;

        return $this;
    }

    /**
     * Get processStartTime.
     *
     * @return \DateTime|null
     */
    public function getProcessStartTime()
    {
        return $this->processStartTime;
    }

    /**
     * Set processEndTime.
     *
     * @param \DateTime|null $processEndTime
     *
     * @return Task
     */
    public function setProcessEndTime($processEndTime = null)
    {
        $this->processEndTime = $processEndTime;

        return $this;
    }

    /**
     * Get processEndTime.
     *
     * @return \DateTime|null
     */
    public function getProcessEndTime()
    {
        return $this->processEndTime;
    }

    /**
     * Set queue.
     *
     * @param int $queue
     *
     * @return Task
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Get queue.
     *
     * @return int
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Set target.
     *
     * @param string $target
     *
     * @return Task
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set targetId.
     *
     * @param string $targetId
     *
     * @return Task
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * Get targetId.
     *
     * @return string
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set params.
     *
     * @param json|null $params
     *
     * @return Task
     */
    public function setParams($params = null)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params.
     *
     * @return json|null
     */
    public function getParams()
    {
        return $this->params;
    }
}
