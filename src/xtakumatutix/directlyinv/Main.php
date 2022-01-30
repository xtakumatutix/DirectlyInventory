<?php

namespace xtakumatutix\directlyinv;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\Config;

use ree_jp\stackstorage\api\StackStorageAPI;

Class Main extends PluginBase implements Listener
{
    public function onEnable() :void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->Config = new Config($this->getDataFolder() . "Config.yml", Config::YAML, array(
            'メッセージ' => '§bインベントリがいっぱいのためスタックストレージに保存しました',
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
                StackStorageAPI::$instance->add($player->getXuid(),$item);
                $player->sendPopup($this->Config->get('メッセージ'));
            }
        }
    }
}