<?php

namespace Quests;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\plugin\PluginBase;
use jojoe77777\FormAPI\Form;
use jojoe77777\FormAPI\FormAPI;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use jackmd\scorefactory\ScoreFactory;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\world\sound\AnvilUseSound;

class Main extends PluginBase implements Listener {
    public $config;
    public $f1;
    public $item;
    private $playerData = [];
    public ScoreFactory $scoreFactory;
    private $scoreboardObjective = "QuestProgress";
    public function onEnable() : void {
        $this->scoreFactory = new ScoreFactory();
        @mkdir($this->getDataFolder() . "players/");
        $this->getServer()->getLogger()->info("Quests Enabled!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onLeave(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        if ($this->playerData[$player->getName()]["inQuest"] === true) {
            $this->getConfigs($player)->setNested("quests.IQ", false);
            $this->getConfigs($player)->save();
        }
    }
    public function startQuest(Player $player, string $quest): void {
        $this->playerData[$player->getName()]["inQuest"] = true;
           $bold = TextFormat::BOLD;
           $yw = TextFormat::YELLOW;
           $gr = TextFormat::RED;
        switch ($quest) {
            case "MS":
                $this->scoreFactory->setObjective($player, "{$bold}{$yw}»{$gr} Quests{$bold}{$yw} «", ScoreFactory::SORT_ORDER_ASCENDING, ScoreFactory::DISPLAY_SLOT_SIDEBAR, $this->scoreboardObjective);
                $progress = $this->config->getNested("quests.MS", 0);
                $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/25");
                $this->scoreFactory->sendObjective($player);
                $this->scoreFactory->sendLines($player);
                break;

                case "MC":
                    $this->scoreFactory->setObjective($player, "{$bold}{$yw}»{$gr} Quests{$bold}{$yw} «", ScoreFactory::SORT_ORDER_ASCENDING, ScoreFactory::DISPLAY_SLOT_SIDEBAR, $this->scoreboardObjective);
                    $progress = $this->config->getNested("quests.MC", 0);
                    $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/10");
                    $this->scoreFactory->sendObjective($player);
                    $this->scoreFactory->sendLines($player);
                break;

                 case "KC":
                    $this->scoreFactory->setObjective($player, "{$bold}{$yw}»{$gr} Quests{$bold}{$yw} «", ScoreFactory::SORT_ORDER_ASCENDING, ScoreFactory::DISPLAY_SLOT_SIDEBAR, $this->scoreboardObjective);
                    $progress = $this->config->getNested("quests.KC", 0);
                    $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/10");
                    $this->scoreFactory->sendObjective($player);
                    $this->scoreFactory->sendLines($player);
                break;

                case "KS":
                    $this->scoreFactory->setObjective($player, "{$bold}{$yw}»{$gr} Quests{$bold}{$yw} «", ScoreFactory::SORT_ORDER_ASCENDING, ScoreFactory::DISPLAY_SLOT_SIDEBAR, $this->scoreboardObjective);
                    $progress = $this->config->getNested("quests.KS", 0);
                    $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/10");
                    $this->scoreFactory->sendObjective($player);
                    $this->scoreFactory->sendLines($player);
                break;

                case "IO":
                    $this->scoreFactory->setObjective($player, "{$bold}{$yw}»{$gr} Quests{$bold}{$yw} «", ScoreFactory::SORT_ORDER_ASCENDING, ScoreFactory::DISPLAY_SLOT_SIDEBAR, $this->scoreboardObjective);
                    $progress = $this->config->getNested("quests.IO", 0);
                    $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/10");
                    $this->scoreFactory->sendObjective($player);
                    $this->scoreFactory->sendLines($player);
                break;
              
            // Add other quests here
    
            default:
                $player->sendMessage("Invalid quest selected.");
                break;
        }
    }
    public function onKill(EntityDeathEvent $entity) {
        $victim = $entity->getEntity();
        $cause = $victim->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent and $cause->getDamager() instanceof Player) {
            $player = $cause->getDamager();
            // ADD THE SHEEP ENTITY HERE 
                $sheep = null;
                $cows = null;


            // -------------------
            $g = $this->playerData[$player->getName()];
            $v = $this->getConfigs($player)->getNested("quests.KS");
           $n = $this->getConfigs($player)->getNested("quests.KC");

            // 10 SHEEPS

            if ($sheep === "Sheep" && $g["inQuest"] === true && $v !== null) {
    
                // Check if it's already completed
                if ($v === 10) {
                    $this->getConfigs($player)->setNested("quests.KS", null);
                    $this->getConfigs($player)->setNested("quests.KC", 0);
                    $this->getConfigs($player)->save();
        
                    $player->sendMessage("Finished.");
                    // Play Sound
                    $bl = TextFormat::BOLD;
                    $gr = TextFormat::GREEN;
                    $sound = new AnvilUseSound();
                    $world = $player->getWorld();
                    $world->addSound($player->getPosition(), $sound);
                } else if ($v < 25) { // Check if it's less than 25
        
                    $progress = $this->config->getNested("quests.KS");
                    $progress++;
                    $this->config->setNested("quests.KS", $progress);
                    $this->config->save();
        
                    $this->updateQuestProgress($player, "KS", $progress);
        
                    // Check if it's completed after the increment
                    if ($progress === 10) {
                        // Play Sound and send the title
                        $bl = TextFormat::BOLD;
                        $gr = TextFormat::GREEN;
                        $sound = new AnvilUseSound();
                        $world = $player->getWorld();
                        $world->addSound($player->getPosition(), $sound);
                        $player->sendTitle("{$bl}{$gr}LEVEL UP!");
                        $player->sendSubTitle("Claim your reward!");
                    }
                }
                // -----------------------------------------
                //  10 COWS -------------------------------------
            } else if ($cows === "Cows" && $g["inQuest"] === true && $n !== null) {
                if ($n === 10) {
                    $this->getConfigs($player)->setNested("quests.IO", 0);
                    $this->getConfigs($player)->setNested("quests.KC", null);
                    $this->getConfigs($player)->save();
                    $progress = $this->config->getNested("quests.KC");
        
                    $progress++;
                    $this->config->setNested("quests.KC", $progress);
                    $this->config->save();
                    $this->updateQuestProgress($player, "KC", $progress);
                    $player->sendMessage("Finished.");
        
                    // Play Sound and send the title
                    $bl = TextFormat::BOLD;
                    $gr = TextFormat::GREEN;
                    $sound = new AnvilUseSound();
                    $world = $player->getWorld();
                    $world->addSound($player->getPosition(), $sound);
                    $player->sendTitle("{$bl}{$gr}LEVEL UP!");
                    $player->sendSubTitle("Claim your reward!");
                } else if ($n < 10) { // Check if it's less than 9
                    $progress = $this->config->getNested("quests.MC");
                    $progress++;
                    $this->config->setNested("quests.KC", $progress);
                    $this->config->save();
        
                    $this->updateQuestProgress($player, "KC", $progress);
        
                    // Check if it's completed after the increment
                    if ($progress === 10) {
                        // Play Sound and send the title
                        $bl = TextFormat::BOLD;
                        $gr = TextFormat::GREEN;
                        $sound = new AnvilUseSound();
                        $world = $player->getWorld();
                        $world->addSound($player->getPosition(), $sound);
                        $player->sendTitle("{$bl}{$gr}LEVEL UP!");
                        $player->sendSubTitle("Claim your reward!");
                    }
                }
            }
        }
    }


    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $g = $this->playerData[$player->getName()];
        $v = $this->getConfigs($player)->getNested("quests.MS");
        $n = $this->getConfigs($player)->getNested("quests.MC");
    
        // STONE ORE --------------------------------------
        if ($block->getName() === "Stone" && $g["inQuest"] === true && $v !== null) {
    
            // Check if it's already completed
            if ($v === 25) {
                $this->getConfigs($player)->setNested("quests.MS", null);
                $this->getConfigs($player)->setNested("quests.MC", 0);
                $this->getConfigs($player)->save();
    
                $player->sendMessage("Finished.");
                // Play Sound
                $bl = TextFormat::BOLD;
                $gr = TextFormat::GREEN;
                $sound = new AnvilUseSound();
                $world = $player->getWorld();
                $world->addSound($player->getPosition(), $sound);
            } else if ($v < 25) { // Check if it's less than 25
    
                $progress = $this->config->getNested("quests.MS");
                $progress++;
                $this->config->setNested("quests.MS", $progress);
                $this->config->save();
    
                $this->updateQuestProgress($player, "MS", $progress);
    
                // Check if it's completed after the increment
                if ($progress === 25) {
                    // Play Sound and send the title
                    $bl = TextFormat::BOLD;
                    $gr = TextFormat::GREEN;
                    $sound = new AnvilUseSound();
                    $world = $player->getWorld();
                    $world->addSound($player->getPosition(), $sound);
                    $player->sendTitle("{$bl}{$gr}LEVEL UP!");
                    $player->sendSubTitle("Claim your reward!");
                }
            }
            // -----------------------------------------
            // COAL ORE -------------------------------------
        } else if ($block->getName() === "Coal Ore" && $g["inQuest"] === true && $n !== null) {
            if ($n === 10) {
                $this->getConfigs($player)->setNested("quests.KC", 0);
                $this->getConfigs($player)->setNested("quests.MC", null);
                $this->getConfigs($player)->save();
                $progress = $this->config->getNested("quests.MC");
    
                $progress++;
                $this->config->setNested("quests.MC", $progress);
                $this->config->save();
                $this->updateQuestProgress($player, "MC", $progress);
                $player->sendMessage("Finished.");
    
                // Play Sound and send the title
                $bl = TextFormat::BOLD;
                $gr = TextFormat::GREEN;
                $sound = new AnvilUseSound();
                $world = $player->getWorld();
                $world->addSound($player->getPosition(), $sound);
                $player->sendTitle("{$bl}{$gr}LEVEL UP!");
                $player->sendSubTitle("Claim your reward!");
            } else if ($n < 10) { // Check if it's less than 9
                $progress = $this->config->getNested("quests.MC");
                $progress++;
                $this->config->setNested("quests.MC", $progress);
                $this->config->save();
    
                $this->updateQuestProgress($player, "MC", $progress);
    
                // Check if it's completed after the increment
                if ($progress === 10) {
                    // Play Sound and send the title
                    $bl = TextFormat::BOLD;
                    $gr = TextFormat::GREEN;
                    $sound = new AnvilUseSound();
                    $world = $player->getWorld();
                    $world->addSound($player->getPosition(), $sound);
                    $player->sendTitle("{$bl}{$gr}LEVEL UP!");
                    $player->sendSubTitle("Claim your reward!");
                }
            }
        } else if ($block->getName() === "Iron Ore" && $g["inQuest"] === true && $n !== null) {
            if ($n === 3) {
               
                $this->getConfigs($player)->setNested("quests.IO", null);
                $this->getConfigs($player)->save();
                $progress = $this->config->getNested("quests.IO");
    
                $progress++;
                $this->config->setNested("quests.IO", $progress);
                $this->config->save();
                $this->updateQuestProgress($player, "IO", $progress);
                $player->sendMessage("Finished.");
    
                // Play Sound and send the title
                $bl = TextFormat::BOLD;
                $gr = TextFormat::GREEN;
                $sound = new AnvilUseSound();
                $world = $player->getWorld();
                $world->addSound($player->getPosition(), $sound);
                $player->sendTitle("{$bl}{$gr}LEVEL UP!");
                $player->sendSubTitle("Claim your reward!");
            } else if ($n < 3) { // Check if it's less than 9
                $progress = $this->config->getNested("quests.IO");
                $progress++;
                $this->config->setNested("quests.IO", $progress);
                $this->config->save();
    
                $this->updateQuestProgress($player, "IO", $progress);
    
                // Check if it's completed after the increment
                if ($progress === 3) {
                    // Play Sound and send the title
                    $bl = TextFormat::BOLD;
                    $gr = TextFormat::GREEN;
                    $sound = new AnvilUseSound();
                    $world = $player->getWorld();
                    $world->addSound($player->getPosition(), $sound);
                    $player->sendTitle("{$bl}{$gr}LEVEL UP!");
                    

                    $new = [
                        "quests" => [
                            "MS" => null,
                            "MC" => null,
                            "KC" => null,
                            "KS" => null,
                            "MI" => null,
                            "IQ" => null,
                        ]
                    ];
                    $this->getConfigs($player)->setAll($new);
                    $this->getConfigs($player)->save();
                    $player->sendSubTitle("Claim your reward!");
                }
            }
        }
    }
    

    public function checkAllQuestsCompleted(Player $player): bool {
        // Check if all quests are null, meaning they are completed
        $questsData = $this->getConfigs($player)->get("quests");
        foreach ($questsData as $questProgress) {
            if ($questProgress !== null) {
                return false;
            }
        }
        return true;
    }

    public function updateQuestProgress(Player $player, string $quest, int $progress): void {
        $bold = TextFormat::BOLD;
           $yw = TextFormat::YELLOW;
           $gr = TextFormat::RED;
        switch ($quest) {
            case "MS":
                $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/25  ");
                $this->scoreFactory->sendLines($player);
                $this->setQuestProgress($player, $quest, $progress);
                break;

            case "MC":
                $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/10");
                $this->scoreFactory->sendLines($player);
                $this->setQuestProgress($player, $quest, $progress);

                break;

                case "KC":
                  
                    $progress = $this->config->getNested("quests.KC", 0);
                    $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/10");
            
                    $this->scoreFactory->sendLines($player);
                break;

                case "KS":
                
                    $progress = $this->config->getNested("quests.KS", 0);
                    $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/10");
           
                    $this->scoreFactory->sendLines($player);
                break;

                case "IO":
                    $progress = $this->config->getNested("quests.IO", 0);
                    $this->scoreFactory->setScoreLine($player, 1, TextFormat::AQUA . "{$yw}➤{$gr} Progress:{$yw} $progress/10");
                   
                    $this->scoreFactory->sendLines($player);
                break;
            // Add other quests here
    
            default:
                break;
        }
    }
    
    public function getQuestProgress(Player $player, string $quest): int {
        $questData = $this->config->get("quests");
        return $questData[$quest] ?? 0;
    }

    public function getConfigs(Player $player) {
        $configPath = $this->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml";
        $t = new Config($configPath, Config::YAML);
        return $t;
    }
    
    public function setQuestProgress(Player $player, string $quest, int $progress): void {
        $this->getConfigs($player)->setNested("quests.$quest", $progress);
        $this->getConfigs($player)->save();
    }
    
    public function d(Player $player) {
            
            for ($x = 0; $x <= 4; $x++) {
                $q = $this->getQuestByIndex($x, $player);
                $t = $this->getConfigs($player)->getNested("quests.MS");
                $v = $this->getConfigs($player)->getNested("quests.MC");
                $g = $this->getConfigs($player)->getNested("quests.KC");
                $d = $this->getConfigs($player)->getNested("quests.KS");
                $e = $this->getConfigs($player)->getNested("quests.IO");
                $d = $this->playerData[$player->getName()]["inQuest"] ? "" : "\nCONTINUE";
                
                $bold = TextFormat::BOLD;
                $yw = TextFormat::YELLOW;
                $gr = TextFormat::RED;

                if ($t >= 25) {
                    $d = "\nCLAIM";
                    $gr = TextFormat::DARK_GREEN;
                } else if ($v >= 25) {
                    $d = "\nCLAIM";
                    $gr = TextFormat::DARK_GREEN;   
                }else if ($g >= 10) {
                    $d = "\nCLAIM";
                    $gr = TextFormat::DARK_GREEN;   
                }else if ($d >= 10) {
                    $d = "\nCLAIM";
                    $gr = TextFormat::DARK_GREEN;   
                }else if ($e >= 3) {
                    $d = "\nCLAIM";
                    $gr = TextFormat::DARK_GREEN;   
                }

                if ($q !== null) {
                    switch($x) {
                        case 4:
                            $this->f1 = new MenuOption("Mine Stones:  {$yw}{$t}/25{$gr}{$bold}{$d}");
                        break;
                        case 3:
                            $this->f1 = new MenuOption("Mine Coal Ore: {$yw}{$v}/10{$gr}{$bold}{$d}");
                        break;
                        case 2:
                            $this->f1 = new MenuOption("Kill Cows:{$yw}{$v}/10{$gr}{$bold}{$d}");
                        break;
                        case 1:
                            $this->f1 = new MenuOption("Kill Sheep:{$yw}{$v}/10{$gr}{$bold}{$d}");
                        break;
                        case 0:
                            $this->f1 = new MenuOption("Mine Iron {$yw}{$v}/3{$gr}{$bold}{$d}");
                        break;
                    }
                }

                
        
    }
    return $this->f1;
    }
    public function openQuests($pl) {

       
        $form =  new MenuForm(
            "Quests",
            "Quests",
            [
                $this->d($pl)
            ],

            function(Player $player, int $selected) : void {
                

                // STONE ----
                if ($this->getConfigs($player)->getNested("quests.MS") !== null) {
                    
                    if ($this->getConfigs($player)->getNested("quests.MS") >= 25) {
                        // REWARD HERE DRAGON...
                        $player->sendMessage("SUCCESS!");
                        $this->config->setNested("quests.MS", null);
                        $this->config->setNested("quests.MC", 0);
                        $this->config->save();

                        if (!$this->scoreFactory->hasCache($player)) return;
                        $this->scoreFactory->removeObjective($player);
                        $this->scoreFactory->removeScoreLines($player);
                    } else {
                        $this->startQuest($player, "MS");
                    }

                    // COAL ORE
                } else if ($this->getConfigs($player)->getNested("quests.MC") !== null) {
                    if ($this->getConfigs($player)->getNested("quests.MC") >= 10) {
                        // REWARD HERE DRAGON...
                        $player->sendMessage("SUCCESS!");
                        $this->config->setNested("quests.MC", null);
                        $this->config->setNested("quests.KC", 0);
                        $this->config->save();
                        if (!$this->scoreFactory->hasCache($player)) return;
                        $this->scoreFactory->removeObjective($player);  
                        $this->scoreFactory->removeScoreLines($player);
                    } else {
                        $this->startQuest($player, "MC");
                    }



                    // KILL COWS
                } else if ($this->getConfigs($player)->getNested("quests.KC") !== null) {
                    if ($this->getConfigs($player)->getNested("quests.KC") >= 10) {
                        // REWARD HERE DRAGON...
                        $player->sendMessage("SUCCESS!");
                        $this->config->setNested("quests.KC", null);
                        $this->config->setNested("quests.KS", 0);
                        $this->config->save();
                        if (!$this->scoreFactory->hasCache($player)) return;
                        $this->scoreFactory->removeObjective($player);  
                        $this->scoreFactory->removeScoreLines($player);
                    } else {
                        $this->startQuest($player, "KC");
                    }



                    // KILL SHEEPS
                } else if ($this->getConfigs($player)->getNested("quests.KS") !== null) {
                    if ($this->getConfigs($player)->getNested("quests.KS") >= 10) {
                        // REWARD HERE DRAGON...
                        $player->sendMessage("SUCCESS!");
                        $this->config->setNested("quests.KS", null);
                        $this->config->setNested("quests.IO", 0);
                        $this->config->save();
                        if (!$this->scoreFactory->hasCache($player)) return;
                        $this->scoreFactory->removeObjective($player);  
                        $this->scoreFactory->removeScoreLines($player);
                    } else {
                        $this->startQuest($player, "KS");
                    }


                    // MINE IRON
                } else if ($this->getConfigs($player)->getNested("quests.IO") !== null) {
                    if ($this->getConfigs($player)->getNested("quests.IO") >= 10) {
                        // REWARD HERE DRAGON...
                        $player->sendMessage("SUCCESS!");
                        $this->config->setNested("quests.IO", null);
                        $this->config->save();
                        if (!$this->scoreFactory->hasCache($player)) return;
                        $this->scoreFactory->removeObjective($player);  
                        $this->scoreFactory->removeScoreLines($player);
                        $new = [
                            "quests" => [
                                "MS" => null,
                                "MC" => null,
                                "KC" => null,
                                "KS" => null,
                                "MI" => null,
                                "IQ" => null,
                            ]
                        ];
                        $this->getConfigs($player)->setAll($new);
                        $this->getConfigs($player)->save();
                    } else {
                        $this->startQuest($player, "IO");
                    }
                }



            },
            function(Player $submitter) : void {
                $submitter->sendMessage("closed the menu");
            }
        );
        
        $pl->sendForm($form);
    }

    public function onJoin(PlayerJoinEvent $event): void {
    $player = $event->getPlayer();
    $this->playerData[$player->getName()] = [
        "inQuest" => false,
    ];
    $configPath = $this->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml";
    
    if (!file_exists($configPath)) {
        // Create a new configuration with default values if it doesn't exist
        $defaultData = [
            "quests" => [
                "MS" => 0,
                "MC" => null,
                "KC" => null,
                "KS" => null,
                "MI" => null,
                "IQ" => null,
            ]
        ];
        $config = new Config($configPath, Config::YAML);
        $config->setAll($defaultData);
        $config->save();
    }
    
    $this->config = new Config($configPath, Config::YAML);

    $this->item = VanillaItems::DIAMOND();
    $player->getInventory()->setItem(0, $this->item);
}

        public function onInteract(PlayerInteractEvent $event) {
            $player = $event->getPlayer();
            $player->sendMessage("something worked, ");
            $item = $player->getInventory()->getItemInHand();
            if ($item->getName() === "Diamond") {
               $this->openQuests($player);
            }
        }



      

    public function getQuestByIndex(int $index, Player $player) {
        $q = $this->getConfigs($player);
        $quest = [
            4 => $q->getNested("quests.MS"),
            3 => $q->getNested("quests.MC"),
            2 => $q->getNested("quests.KC"),
            1 => $q->getNested("quests.KS"),
            0 => $q->getNested("quests.MI"),
        ];
        
        return $quest[$index];
    }

    public function openQuests2($quest) {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            
        });
    }
}