<?php

namespace david\entityLimiter;

use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {

    public function onEnable(): void {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }
}