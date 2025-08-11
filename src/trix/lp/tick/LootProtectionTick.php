<?php

namespace trix\lp\tick;

use pocketmine\entity\object\ItemEntity;
use pocketmine\nbt\tag\StringTag;
use pocketmine\scheduler\Task;
use trix\lp\Root;

final class LootProtectionTick extends Task {
    private Root $root;

    public function __construct() {
        $this->root = Root::getInstance();
    }

    // TODO: Maybe set a task for every entity, instead of reaching for each entity in every world
    // Although this has been tested in production with (100+ players) and held up perfectly fine
    public function onRun(): void {
        foreach (Root::getInstance()->getServer()->getWorldManager()->getWorlds() as $world) {

            foreach ($world->getEntities() as $entity) {
                if (!($entity instanceof ItemEntity)) continue;

                $item = $entity->getItem();
                $protectionTag = $item->getNamedTag()->getTag("LootProtected");

                if ($protectionTag instanceof StringTag) {
                    $data = $protectionTag->getValue();
                    $pos = strpos($data, "--$$$--");

                    if ($pos !== false) {
                        $time = (int)substr($data, $pos + 7) - 1;
                        $this->root->removeProtection($item);

                        if ($time > 0) {
                            $this->root->setProtection($item, substr($data, 0, $pos), $time);
                        }
                    }
                }
            }
        }
    }
}