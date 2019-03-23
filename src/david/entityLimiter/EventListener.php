<?php

namespace david\entityLimiter;

use pocketmine\entity\Animal;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Monster;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;

class EventListener implements Listener {

    /** @var Loader */
    private $plugin;

    /** @var Animal[] */
    private $animals = [];

    /** @var Monster[] */
    private $monsters = [];

    /** @var ItemEntity[] */
    private $itemEntities = [];

    /**
     * EventListener constructor.
     *
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @priority HIGHEST
     * @param EntitySpawnEvent $event
     */
    public function onEntitySpawn(EntitySpawnEvent $event): void {
        $entity = $event->getEntity();
        $config = $this->plugin->getConfig();
        if($entity instanceof Human) {
            return;
        }
        $despawn = null;
        if($entity instanceof Animal) {
            $this->animals[$entity->getId()] = $entity;
            if(count($this->animals) > $config->get("maxAnimals")) {
                $despawn = array_shift($this->animals);
            }
        }
        if($entity instanceof Monster) {
            $this->monsters[$entity->getId()] = $entity;
            if(count($this->monsters) > $config->get("maxMonsters")) {
                $despawn = array_shift($this->monsters);
            }
        }
        if($entity instanceof ItemEntity) {
            $this->itemEntities[$entity->getId()] = $entity;
            if(count($this->itemEntities) > $config->get("maxItemEntities")) {
                $despawn = array_shift($this->itemEntities);
            }
        }
        if($despawn === null) {
            return;
        }
        if($despawn->isClosed()) {
            return;
        }
        $despawn->flagForDespawn();
    }

    /**
     * @priority HIGHEST
     * @param EntityDespawnEvent $event
     */
    public function onEntityDespawn(EntityDespawnEvent $event): void {
        $entity = $event->getEntity();
        if(isset($this->animals[$entity->getId()])) {
            unset($this->animals[$entity->getId()]);
        }
        if(isset($this->monsters[$entity->getId()])) {
            unset($this->monsters[$entity->getId()]);
        }
        if(isset($this->itemEntities[$entity->getId()])) {
            unset($this->itemEntities[$entity->getId()]);
        }
    }
}