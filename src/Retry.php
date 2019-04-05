<?php declare(strict_types=1);

namespace Acelot\Helpers;

/**
 * @deprecated Use `repeat` function.
 */
class Retry
{
    public const SECONDS = 1000000;
    public const MILLISECONDS = 1000;

    public const BEFORE_PAUSE_HOOK = 'before';
    public const AFTER_PAUSE_HOOK = 'after';

    /**
     * @var callable
     */
    protected $callable;

    /**
     * @var array[string]callable
     */
    protected $hooks;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var int
     */
    protected $pause;

    /**
     * @var string
     */
    protected $exceptionType;

    /**
     * @param callable $callable Callable
     * @param int      $timeout  Max attempt timeout in microseconds (-1 for infinity)
     * @param int      $count    Max attempt count (-1 for indefinite)
     * @param int      $pause    Pause between attempts in microseconds
     *
     * @return Retry
     */
    public static function create(callable $callable, int $timeout = -1, int $count = -1, int $pause = 0): Retry
    {
        return new self($callable, $timeout, $count, $pause);
    }

    private function __construct(callable $callable, int $timeout = -1, int $count = -1, int $pause = 0)
    {
        $this->callable = $callable;
        $this->hooks = [];
        $this->timeout = $timeout;
        $this->count = $count;
        $this->pause = $pause;
        $this->exceptionType = \Throwable::class;
    }

    /**
     * Runs the retry loop.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function run()
    {
        $count = 0;
        $start = microtime(true);

        while (true) {
            try {
                return call_user_func($this->callable);
            } catch (\Throwable $e) {
                // Check exception
                if (!$e instanceof $this->exceptionType) {
                    throw $e;
                }

                // Check timeout
                if ($this->timeout > -1 && microtime(true) - $start > ($this->timeout / self::SECONDS)) {
                    throw $e;
                }

                // Check count
                if ($this->count > -1 && ++$count >= $this->count) {
                    throw $e;
                }

                // Before pause hook
                if (array_key_exists(self::BEFORE_PAUSE_HOOK, $this->hooks)) {
                    call_user_func($this->hooks[self::BEFORE_PAUSE_HOOK], $e);
                }

                usleep($this->pause);

                // After pause hook
                if (array_key_exists(self::AFTER_PAUSE_HOOK, $this->hooks)) {
                    call_user_func($this->hooks[self::AFTER_PAUSE_HOOK], $e);
                }
            }
        }
    }

    public function getHook(string $hook): ?callable
    {
        return $this->hooks[$hook] ?? null;
    }

    /**
     * Sets the hook callback function which will be called if exceptions will raise.
     *
     * @param string   $hook
     * @param callable $callable
     *
     * @return Retry
     */
    public function setHook(string $hook, callable $callable): self
    {
        $availableHooks = [self::BEFORE_PAUSE_HOOK, self::AFTER_PAUSE_HOOK];

        if (!in_array($hook, $availableHooks)) {
            throw new \InvalidArgumentException('Invalid hook. Available hooks: ' . join(', ', $availableHooks));
        }

        $this->hooks[$hook] = $callable;
        return $this;
    }

    public function removeHook(string $hook): self
    {
        unset($this->hooks[$hook]);
        return $this;
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }

    /**
     * Sets the main callback function which will be called during tries.
     *
     * @param callable $callable
     *
     * @return Retry
     */
    public function setCallable(callable $callable): self
    {
        $this->callable = $callable;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Sets the maximum attempt timeout in microseconds. Pass -1 for infinity.
     *
     * @param int $timeout
     *
     * @return Retry
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Sets the maximum attempt count. Pass -1 for indefinite.
     *
     * @param int $count
     *
     * @return Retry
     */
    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    public function getPause(): int
    {
        return $this->pause;
    }

    /**
     * Sets the pause between tries in microseconds
     *
     * @param int $pause
     *
     * @return Retry
     */
    public function setPause(int $pause): self
    {
        $this->pause = $pause;
        return $this;
    }

    public function getExceptionType(): string
    {
        return $this->exceptionType;
    }

    /**
     * Sets the exception class name which should be catched during tries.
     *
     * @param string $exceptionType
     *
     * @return self
     */
    public function setExceptionType(string $exceptionType): self
    {
        try {
            $ref = new \ReflectionClass($exceptionType);
            if (!$ref->implementsInterface(\Throwable::class)) {
                throw new \InvalidArgumentException('Exception class must implement Throwable interface');
            }
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException('Exception class not found');
        }

        $this->exceptionType = $exceptionType;
        return $this;
    }
}
