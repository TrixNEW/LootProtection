<?php
declare(strict_types=1);
namespace trix\lp;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\ConfigLoadException;
use pocketmine\utils\SingletonTrait;

final class Root extends PluginBase {
    use SingletonTrait;

    public static int $duration;

    protected function onLoad(): void {
        self::setInstance($this);
    }

    protected function onEnable(): void {
        $key = $this->getConfig()->get("protection-duration");
        if (!is_int($key)) {
            throw new ConfigLoadException("The duration configured is not a valid number");
        }
        self::$duration = $key;
        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
    }
}