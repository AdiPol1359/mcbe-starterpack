<?php

namespace ClearLagg;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\entity\DroppedItem;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Creature;

class Loader extends PluginBase {

  protected $exemptedEntities = [];

  public function onEnable() {
    $this->getServer()->getCommandMap()->register("clearlagg", new ClearLaggCommand($this));
  }

  /**
   * @return int
   */
  public function removeEntities() {
    $i = 0;
    foreach($this->getServer()->getLevels() as $level) {
      foreach($level->getEntities() as $entity) {
        if(!$this->isEntityExempted($entity) && !($entity instanceof Creature)) {
          $entity->close();
          $i++;
        }
      }
    }
    return $i;
  }

  /**
   * @return int
   */
  public function removeMobs() {
    $i = 0;
    foreach($this->getServer()->getLevels() as $level) {
      foreach($level->getEntities() as $entity) {
        if(!$this->isEntityExempted($entity) && $entity instanceof Creature && !($entity instanceof Human)) {
          $entity->close();
          $i++;
        }
      }
    }
    return $i;
  }

  /**
   * @return array
   */
  public function getEntityCount() {
    $ret = [0, 0, 0];
    foreach($this->getServer()->getLevels() as $level) {
      foreach($level->getEntities() as $entity) {
        if($entity instanceof Human) {
          $ret[0]++;
        } else if($entity instanceof Creature) {
          $ret[1]++;
        } else {
          $ret[2]++;
        }
      }
    }
    return $ret;
  }

  /**
   * @param Entity $entity
   */
  public function exemptEntity(Entity $entity) {
    $this->exemptedEntities[$entity->getID()] = $entity;
  }

  /**
   * @param Entity $entity
   * @return bool
   */
  public function isEntityExempted(Entity $entity) {
    return isset($this->exemptedEntities[$entity->getID()]);
  }

} 
