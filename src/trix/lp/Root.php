<?php
declare(strict_types=1);

namespace trix\lp;

use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use trix\lp\exception\InvalidKeyException;
use trix\lp\tick\LootProtectionTick;

final class Root extends PluginBase {
    use SingletonTrait;

    public static ?int $duration;

    protected function onLoad(): void {
        self::setInstance($this);
    }

    /**
     * @throws InvalidKeyException
     */
    protected function onEnable(): void {
        $key = $this->getConfig()->get("protection-duration");

        if (!is_int($key)) {
            throw new InvalidKeyException("The duration configured is not a valid number");
        }

        self::$duration = $key;

        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new LootProtectionTick(), 20);
    }

    public function setProtection(Item $item, string $player, $time): Item {
        $item->getNamedTag()->setString("LootProtected", "$player--$$$--$time");
        return $item;
    }

    public function removeProtection(Item $item): Item {
        $item->getNamedTag()->removeTag("LootProtected");
        return $item;
    }
}