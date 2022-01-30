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
        $this->Config = new Config($this->getDataFolder() . "Config.yml", Config::YAML, array(
            'メッセージ' => '§bインベントリがいっぱいのため下に落としました',
        ));
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
                $player->sendPopup($this->Config->get('メッセージ'));
            }
        }
    }
}
