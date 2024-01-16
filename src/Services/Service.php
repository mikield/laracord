<?php

namespace Laracord\Services;

use Discord\DiscordCommandClient as Discord;
use Laracord\Console\Commands\BootCommand as Console;
use Laracord\Laracord;
use Laracord\Services\Contracts\Service as ServiceContract;
use Laracord\Services\Exceptions\InvalidServiceInterval;

abstract class Service implements ServiceContract
{
    /**
     * The bot instance.
     *
     * @var \Laracord\Laracord
     */
    protected $bot;

    /**
     * The console instance.
     *
     * @var \Laracord\Console\Commands\BootCommand
     */
    protected $console;

    /**
     * The Discord instance.
     *
     * @var \Discord\DiscordCommandClient;
     */
    protected $discord;

    /**
     * The service name.
     */
    protected string $name = '';

    /**
     * The loop interval.
     */
    protected int $interval = 5;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(Laracord $bot)
    {
        $this->bot = $bot;
        $this->console = $bot->getConsole();
        $this->discord = $bot->getDiscord();
    }

    /**
     * Make a new service instance.
     */
    public static function make(Laracord $bot): self
    {
        return new static($bot);
    }

    /**
     * Handle the service.
     *
     * @return void
     */
    abstract public function handle();

    /**
     * Boot the service.
     */
    public function boot(): void
    {
        if ($this->getInterval() < 1) {
            throw new InvalidServiceInterval($this->getName());
        }

        $this->bot->getLoop()->addPeriodicTimer(
            $this->getInterval(),
            fn () => $this->handle()
        );
    }

    /**
     * Get the Discord client.
     */
    public function getDiscord(): Discord
    {
        return $this->discord;
    }

    /**
     * Get the bot instance.
     */
    public function getBot(): Laracord
    {
        return $this->bot;
    }

    /**
     * Get the console instance.
     */
    public function getConsole(): Console
    {
        return $this->console;
    }

    /**
     * Get the loop instance.
     */
    public function getLoop()
    {
        return $this->getBot()->getLoop();
    }

    /**
     * Get the loop interval.
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * Set the loop interval.
     */
    public function interval(int $interval): self
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * Get the service name.
     */
    public function getName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        return $this->name = class_basename(static::class);
    }
}