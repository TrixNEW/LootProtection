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
    public static string $popupMessage;

    protected function onLoad(): void {
        self::setInstance($this);
    }

    protected function onEnable(): void {
        $this->saveDefaultConfig();

        $duration = $this->getConfig()->get("protection-duration", 3);
        if (!is_int($duration) || $duration < 1) {
            throw new ConfigLoadException("protection-duration must be a positive number");
        }

        $popup = $this->getConfig()->get("messages.popup", "&cLoot protected for &e{SECONDS}s");

        if (!is_string($popup)) {
            throw new ConfigLoadException("messages.popup must be text");
        }

        self::$duration = $duration;
        self::$popupMessage = $popup;

        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public static function format(string $message, array $replace = []) : string{
        foreach($replace as $key => $value){
            $message = str_replace("{" . $key . "}", (string) $value, $message);
        }

        return str_replace("&", "§", $message);
    }
}