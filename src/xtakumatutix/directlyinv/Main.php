<?php

namespace xtakumatutix\directlyinv;

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
		$remainder = $this->tryAddItemToInventory($player, $dropitem);
		$event->setDrops($remainder);
		if(count($remainder) > 0){
			$player->sendPopup($this->getConfig()->getNested('message.inventory-full'));
		}
    }

	/**
	 * @param Player $player
	 * @param Item[] $items
	 * @return Item[] 追加されなかった分のアイテム
	 */
	private function tryAddItemToInventory(Player $player, array $items): array{
		$remainder = [];
		foreach($items as $item){
			if ($player->getInventory()->canAddItem($item))
			{
				$player->getInventory()->addItem($item);
			}else{
				$remainder[] = $item;
			}
		}
		return $remainder;
	}
}
