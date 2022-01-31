<?php

namespace xtakumatutix\directlyinv;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
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
		$remainder = $this->tryAddItemToInventory($player->getInventory(), $dropitem);
		$event->setDrops($remainder);
		if(count($remainder) > 0){
			$player->sendPopup($this->getConfig()->getNested('message.inventory-full'));
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
}
