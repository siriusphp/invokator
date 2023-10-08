<?php
declare(strict_types=1);

namespace Sirius\StackRunner;

class Stack extends \SplPriorityQueue
{
    protected int $index = 0;

    protected bool $reversed = false;

    public function __construct(bool $reversed = false)
    {
        $this->reversed = $reversed;
    }

    public function add($callable, int $priority = 0)
    {
        parent::insert($callable, [$priority, $this->index++]);

        return $this;
    }

    public function compare($priority1, $priority2): int
    {
        $sign = $this->reversed ? -1 : 1;
        if ($sign * $priority1[0] < $sign * $priority2[0]) {
            return 1;
        }
        if ($sign * $priority1[0] > $sign * $priority2[0]) {
            return -1;
        }

        return $sign * $priority1[1] < $sign * $priority2[1] ? 1 : -1;
    }

    public function __serialize()
    {
        $data         = [];
        $extractFlags = $this->getExtractFlags();
        $this->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        foreach ($this as $v) {
            $data[] = [
                'callable' => $v['data'],
                'priority' => $v['priority'][0]
            ];
        }
        $this->setExtractFlags($extractFlags);

        return $data;
    }

    public function __unserialize(array $data)
    {
        foreach ($data as $v) {
            $this->add($v['callable'], $v['priority']);
        }
    }
}
