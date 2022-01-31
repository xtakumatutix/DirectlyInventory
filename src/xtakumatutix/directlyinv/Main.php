<?php

namespace xtakumatutix\directlyinv;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;

Class Main extends PluginBase implements Listener
{
    private bool $useStackStorage = false;

    public function onEnable() :void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();

        if($this->getServer()->getPluginManager()->getPlugin("StackStorage") !== null){
            $this->useStackStorage = true;
        }

    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $dropitem = $event->getDrops();
        $event->setDrops([]);

        if($this->useStackStorage and !$this->getConfig()->getNested("stackstorage.player-inventory-first")){
            $this->sendItemsToStackStorage($player, $dropitem);
            return;
        }

        $remainder = $this->tryAddItemToInventory($player->getInventory(), $dropitem);
        if(count($remainder) > 0){
            if($this->useStackStorage){
                $this->sendItemsToStackStorage($player, $remainder);
                $message = $this->getConfig()->getNested("message.inventory-full-with-stackstorage");
            }else{
                $event->setDrops($remainder);
                $message = $this->getConfig()->getNested("message.inventory-full");
            }
            $player->sendPopup($message);
        }
    }

    /**
     * @param Inventory $inventory
     * @param Item[] $items
     * @return Item[] 追加されなかった分のアイテム
     */
    private function tryAddItemToInventory(Inventory $inventory, array $items): array{
        $remainder = [];
        foreach($items as $item){
            if ($inventory->canAddItem($item))
            {
                $inventory->addItem($item);
            }else{
                $remainder[] = $item;
            }
        }
        return $remainder;
    }

    /**
     * @param Player $player
     * @param Item[] $items
     */
    private function sendItemsToStackStorage(Player $player, array $items){
        foreach($items as $item){
            StackStorageAPI::$instance->add($player->getXuid(), $item);
        }
    }
}
