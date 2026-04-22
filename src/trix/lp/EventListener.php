<?php
declare(strict_types=1);

namespace trix\lp;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use trix\lp\cache\LootProtectionCache;

final class EventListener implements Listener {

    public function onProtectedDeath(PlayerDeathEvent $ev): void {
        $last = $ev->getPlayer()->getLastDamageCause();
        if (!($last instanceof EntityDamageByEntityEvent)) return;

        $damager = $last->getDamager();
        if (!($damager instanceof Player)) return;

        $drops = $ev->getDrops();
        if (empty($drops)) return;

        $value = $damager->getName() . '--$$$--' . (time() + Root::$duration);
        foreach ($drops as $drop) {
            $drop->getNamedTag()->setString('LootProtected', $value);
        }
        $ev->setDrops($drops);
    }

    public function onPickUp(EntityItemPickupEvent $ev): void {
        $player = $ev->getEntity();
        if (!($player instanceof Player)) return;

        $entityId   = $ev->getOrigin()->getId();
        $playerName = $player->getName();

        if (LootProtectionCache::has($entityId)) {
            if (!LootProtectionCache::evaluate($entityId, $playerName)) {
                $ev->cancel();
            }
            return;
        }

        $tag = $ev->getItem()->getNamedTag()->getTag('LootProtected');
        if (!($tag instanceof StringTag)) return;

        $parsed = LootProtectionCache::parse($tag->getValue());
        if ($parsed === null) return;

        [$owner, $expiry] = $parsed;
        if (time() >= $expiry) return;

        if ($playerName === $owner) return;

        LootProtectionCache::add($entityId, $owner, $expiry);
        $ev->cancel();
    }

    public function onDespawn(EntityDespawnEvent $ev): void {
        if (!($ev->getEntity() instanceof ItemEntity)) return;
        LootProtectionCache::remove($ev->getEntity()->getId());
    }
}