<?php

namespace trix\lp;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;

final class EventListener implements Listener {

    public function onProtectedDeath(PlayerDeathEvent $ev): void {
        $player = $ev->getPlayer();
        $last = $player->getLastDamageCause();

        if (!($last instanceof EntityDamageByEntityEvent)) return;

        $damager = $last->getDamager();
        if (!($damager instanceof Player)) return;

        $drops = $ev->getDrops();
        if (empty($drops)) return;

        foreach ($drops as &$drop) {
            $drop = Root::getInstance()->setProtection($drop, $damager->getName(), Root::$duration);
        }

        $ev->setDrops($drops);
    }

    public function onPickUp(EntityItemPickupEvent $ev): void {
        $player = $ev->getEntity();

        if (!($player instanceof Player)) return;

        $item = $ev->getItem();
        $namedTag = $item->getNamedTag();

        $protectionTag = $namedTag->getTag("LootProtected");
        if (!($protectionTag instanceof StringTag)) return;

        $protectionData = $protectionTag->getValue();
        $owner = strstr($protectionData, "--$$$--", true);

        if (!$owner) return;

        if ($player->getName() === $owner) {
            $ev->setItem(Root::getInstance()->removeProtection($item));
        } else {
            $ev->cancel();
        }
    }
}