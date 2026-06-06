<?php

declare(strict_types=1);

namespace Sirius\Invokator;

/**
 * @extends \SplPriorityQueue<array<int>, mixed>
 */
class CallableCollection extends \SplPriorityQueue
{
    protected int $index = 0;

    // NOT promoted on purpose: __unserialize() bypasses the constructor, so the
    // property needs a class-level default to stay initialized after unserializing.
    protected bool $reversed = false;

    public function __construct(bool $reversed = false)
    {
        $this->reversed = $reversed;
    }

    public function add(mixed $callable, int $priority = 0): self
    {
        parent::insert($callable, [$priority, $this->index++]);

        return $this;
    }

    /**
     * @param array<int> $priority1
     * @param array<int> $priority2
     */
    public function compare($priority1, $priority2): int
    {
        $sign = $this->reversed ? -1 : 1;
        if ($sign * ($priority1[0] ?? 0) < $sign * ($priority2[0] ?? 0)) {
            return -1;
        }
        if ($sign * ($priority1[0] ?? 0) > $sign * ($priority2[0] ?? 0)) {
            return 1;
        }

        return $sign * ($priority1[1] ?? 0) < $sign * ($priority2[1] ?? 0) ? 1 : -1;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function __serialize(): array
    {
        $data         = [];
        $extractFlags = $this->getExtractFlags();
        $this->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        /** @var array<mixed> $v */
        foreach ($this as $v) {
            $data[] = [
                'callable' => $v['data'],
                'priority' => is_array($v['priority']) ? ($v['priority'][0] ?? 0) : 0
            ];
        }
        $this->setExtractFlags($extractFlags);

        return $data;
    }

    /**
     * @param array<mixed> $data
     */
    public function __unserialize(array $data): void
    {
        /** @var array<mixed> $v */
        foreach ($data as $v) {
            $this->add($v['callable'], intval($v['priority'])); //@phpstan-ignore-line
        }
    }
}
