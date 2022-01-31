<?php

namespace xtakumatutix\directlyinv;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\Config;

Class Main extends PluginBase implements Listener
{
    public function onEnable() :void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $dropitem = $event->getDrops();
        $event->setDrops([]);
        foreach($dropitem as $item){

            if ($player->getInventory()->canAddItem($item))
            {
                $player->getInventory()->addItem($item);
            }else{
                $world = $player->getWorld();
                $position = $player->getPosition();
                $world->dropItem($position, $item);
                $player->sendPopup($this->getConfig()->getNested('message.inventory-full'));
            }
        }
    }
}
