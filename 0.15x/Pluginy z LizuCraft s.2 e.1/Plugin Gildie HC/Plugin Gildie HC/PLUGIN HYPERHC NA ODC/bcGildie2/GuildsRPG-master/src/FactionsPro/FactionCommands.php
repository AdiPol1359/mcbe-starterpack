<?php

namespace FactionsPro;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\PluginTask;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\level\level;
use pocketmine\level\Position;
use onebone\economyapi\EconomyAPI;
use pocketmine\item\Item;


class FactionCommands {

    public $plugin;

    public function __construct(FactionMain $pg) {
        $this->plugin = $pg;
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if ($sender instanceof Player) {
			$player = $sender->getPlayer()->getName();
			$create = $this->plugin->prefs->get("CreateCost");
			$claim = $this->plugin->prefs->get("ClaimCost");
			$oclaim = $this->plugin->prefs->get("OverClaimCost");
			$allyr = $this->plugin->prefs->get("AllyCost");
			$allya = $this->plugin->prefs->get("AllyPrice");
			$home = $this->plugin->prefs->get("SetHomeCost");
            $player = $sender->getPlayer()->getName();
            if (strtolower($command->getName('g'))) {
                if (empty($args)) {
                    $sender->sendMessage($this->plugin->formatMessage("Użyj: /g pomoc aby wyświetlić wszystkie komendy"));
                    return true;
                }
                if (count($args == 2)) {

                    /////////////////////////////// CREATE ///////////////////////////////

                    if ($args[0] == "zaloz") {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g zaloz <nazwa>"));
                            return true;
                        }
                        if ($this->plugin->isNameBanned($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Ta nazwa jest niedozwolona"));
                            return true;
                        }
                        if ($this->plugin->factionExists($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Taka gildia już istnieje"));
                            return true;
                        }
                        if (strlen($args[1]) > $this->plugin->prefs->get("MaxFactionNameLength")) {
                            $sender->sendMessage($this->plugin->formatMessage("Ta nazwa jest za długa maksymaln liczba liter/liczb to ". $this->plugin->prefs->get("MaxFactionNameLength")));
                            return true;
                        }
                        if ($this->plugin->isInFaction($sender->getName())) {
                            $sender->sendMessage($this->plugin->formatMessage("Najpierw musisz opuścić aktualną gildie"));
                            return true;
						} else {
							if($sender->getInventory()->contains(Item::get(264, 0, 64))){
						    if($sender->getInventory()->contains(Item::get(265, 0, 64))){
							if($sender->getInventory()->contains(Item::get(266, 0, 64))){
						    if($sender->getInventory()->contains(Item::get(322, 0, 16))){
						    if($sender->getInventory()->contains(Item::get(129, 0, 16))){
							if($sender->getInventory()->contains(Item::get(145, 0, 8))){
							if($sender->getInventory()->contains(Item::get(116, 0, 8))){
							$sender->getInventory()->removeItem(Item::get(264, 0, 64));
							$sender->getInventory()->removeItem(Item::get(265, 0, 64));
							$sender->getInventory()->removeItem(Item::get(266, 0, 64));
							$sender->getInventory()->removeItem(Item::get(322, 0, 16));
							$sender->getInventory()->removeItem(Item::get(116, 0, 8));
							$sender->getInventory()->removeItem(Item::get(129, 0, 16));
							$sender->getInventory()->removeItem(Item::get(145, 0, 8));
							$factionName = $args[1];
							$player = strtolower($player);
							$rank = "Leader";
							$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
							$stmt->bindValue(":player", $player);
							$stmt->bindValue(":faction", $factionName);
							$stmt->bindValue(":rank", $rank);
							$result = $stmt->execute();
							$sender->sendMessage($this->plugin->formatMessage("§a • Stworzyłeś gildie • ", true));
							return true;
							}
							else{
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §bDiamentów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (16) §6Refili §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §7Żelaza §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (8) §bEnchantów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §eZłota §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (10) §cCobblex §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§6 • (8) §8Kowadeł §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
						   }
						}
						else{
									$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §bDiamentów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (16) §6Refili §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §7Żelaza §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (8) §bEnchantów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §eZłota §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (10) §cCobblex §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§6 • (8) §8Kowadeł §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
						   }
						}
						else{
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §bDiamentów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (16) §6Refili §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §7Żelaza §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (8) §bEnchantów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §eZłota §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (10) §cCobblex §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§6 • (8) §8Kowadeł §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
						   }
						}
						else{
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §bDiamentów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (16) §6Refili §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §7Żelaza §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (8) §bEnchantów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §eZłota §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (10) §cCobblex §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§6 • (8) §8Kowadeł §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
					      }
						}
						else{
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §bDiamentów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (16) §6Refili §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §7Żelaza §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (8) §bEnchantów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §eZłota §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (10) §cCobblex §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§6 • (8) §8Kowadeł §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
						}
						else{
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §bDiamentów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (16) §6Refili §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §7Żelaza §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (8) §bEnchantów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §eZłota §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (10) §cCobblex §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§6 • (8) §8Kowadeł §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
						   }
						}
						else{
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §bDiamentów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (16) §6Refili §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §7Żelaza §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (8) §bEnchantów §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (64) §eZłota §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§a • (10) §cCobblex §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§6 • (8) §8Kowadeł §a• "));
								$sender->sendMessage($this->plugin->formatMessage("§7 • =====[§cIty]§7===== • "));
						   }
					}
					}
					
					
                    /////////////////////////////// INVITE ///////////////////////////////

                    if ($args[0] == "dodaj") {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g dodaj <gracz>"));
                            return true;
                        }
                        if ($this->plugin->isFactionFull($this->plugin->getPlayerFaction($player))) {
                            $sender->sendMessage($this->plugin->formatMessage("Gildia jest pełna, zwolnij miejsca wyrzucając nieaktywnych graczy"));
                            return true;
                        }
                        $invited = $this->plugin->getServer()->getPlayerExact($args[1]);
                        if (!($invited instanceof Player)) {
                            $sender->sendMessage($this->plugin->formatMessage("Ten gracz nie jest online"));
                            return true;
                        }
                        if ($this->plugin->isInFaction($invited) == true) {
                            $sender->sendMessage($this->plugin->formatMessage("Ten gracz nalezy juz do gildi"));
                            return true;
                        }
                        if ($this->plugin->prefs->get("OnlyLeadersAndOfficersCanInvite")) {
                            if (!($this->plugin->isOfficer($player) || $this->plugin->isLeader($player))) {
                                $sender->sendMessage($this->plugin->formatMessage("Tylko liderzy oraz oficerzy mogą zapraszać!"));
                                return true;
                            }
                        }
                        if ($invited->getName() == $player) {

                            $sender->sendMessage($this->plugin->formatMessage("Nie możesz zaprosić samego siebie"));
                            return true;
                        }

                        $factionName = $this->plugin->getPlayerFaction($player);
                        $invitedName = $invited->getName();
                        $rank = "Member";

                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO confirm (player, faction, invitedby, timestamp) VALUES (:player, :faction, :invitedby, :timestamp);");
                        $stmt->bindValue(":player", $invitedName);
                        $stmt->bindValue(":faction", $factionName);
                        $stmt->bindValue(":invitedby", $sender->getName());
                        $stmt->bindValue(":timestamp", time());
                        $result = $stmt->execute();
                        $sender->sendMessage($this->plugin->formatMessage("$invitedName został zaproszony do gildi> $factionName.", true));
                        $invited->sendMessage($this->plugin->formatMessage("Zostałeś zaproszony do gildi> $factionName. Wpisz '/g akceptuj' aby zaakceptowac lub '/g odrzuc' by odrzucic!", true));
                    }

                    /////////////////////////////// LEADER ///////////////////////////////

                    if ($args[0] == "lider") {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g lider <gracz>"));
                            return true;
                        }
                        if (!$this->plugin->isInFaction($sender->getName())) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz mieć gildie aby to wykonać!"));
                            return true;
                        }
                        if (!$this->plugin->isLeader($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby to wykonać"));
                            return true;
                        }
                        if ($this->plugin->getPlayerFaction($player) != $this->plugin->getPlayerFaction($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Ten gracz nie należy do twojej gildi!"));
                            return true;
                        }
                        if (!($this->plugin->getServer()->getPlayerExact($args[1]) instanceof Player)) {
                            $sender->sendMessage($this->plugin->formatMessage("Ten gracz jest offline"));
                            return true;
                        }
                        if ($args[1] == $sender->getName()) {

                            $sender->sendMessage($this->plugin->formatMessage("Nie możesz oddać lidera samemu sobie"));
                            return true;
                        }
                        $factionName = $this->plugin->getPlayerFaction($player);

                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
                        $stmt->bindValue(":player", $player);
                        $stmt->bindValue(":faction", $factionName);
                        $stmt->bindValue(":rank", "Member");
                        $result = $stmt->execute();

                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
                        $stmt->bindValue(":player", $args[1]);
                        $stmt->bindValue(":faction", $factionName);
                        $stmt->bindValue(":rank", "Leader");
                        $result = $stmt->execute();


                        $sender->sendMessage($this->plugin->formatMessage("Już nie jesteś liderem", true));
                        $this->plugin->getServer()->getPlayerExact($args[1])->sendMessage($this->plugin->formatMessage("Zostałeś liderem gildi $factionName!", true));
                        $this->plugin->updateTag($sender->getName());
                        $this->plugin->updateTag($this->plugin->getServer()->getPlayerExact($args[1])->getName());
                    }

                    /////////////////////////////// PROMOTE ///////////////////////////////

                    if ($args[0] == "awansuj") {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g awansuj <gracz>"));
                            return true;
                        }
                        if (!$this->plugin->isInFaction($sender->getName())) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być w gildi aby to wykonać"));
                            return true;
                        }
                        if (!$this->plugin->isLeader($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby to wykonać"));
                            return true;
                        }
                        if ($this->plugin->getPlayerFaction($player) != $this->plugin->getPlayerFaction($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Gracz nie należy do gildi!"));
                            return true;
                        }
                        if ($args[1] == $sender->getName()) {
                            $sender->sendMessage($this->plugin->formatMessage("Nie możesz awansować siebie!"));
                            return true;
                        }

                        if ($this->plugin->isOfficer($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Ten gracz jest juz oficerem!"));
                            return true;
                        }
                        $factionName = $this->plugin->getPlayerFaction($player);
                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
                        $stmt->bindValue(":player", $args[1]);
                        $stmt->bindValue(":faction", $factionName);
                        $stmt->bindValue(":rank", "Officer");
                        $result = $stmt->execute();
                        $player = $this->plugin->getServer()->getPlayerExact($args[1]);
                        $sender->sendMessage($this->plugin->formatMessage("$args[1] został awansowany", true));

                        if ($player instanceof Player) {
                            $player->sendMessage($this->plugin->formatMessage("Zostałeś awansowany na oficera w gildi $factionName!", true));
                            $this->plugin->updateTag($this->plugin->getServer()->getPlayerExact($args[1])->getName());
                            return true;
                        }
                    }

                    /////////////////////////////// DEMOTE ///////////////////////////////

                    if ($args[0] == "zwolnij") {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g zwolnij <gracz>"));
                            return true;
                        }
                        if ($this->plugin->isInFaction($sender->getName()) == false) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz mieć gildie aby to wykonać"));
                            return true;
                        }
                        if ($this->plugin->isLeader($player) == false) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby to wykonać"));
                            return true;
                        }
                        if ($this->plugin->getPlayerFaction($player) != $this->plugin->getPlayerFaction($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Ten gracz nie należy do gildi"));
                            return true;
                        }

                        if ($args[1] == $sender->getName()) {
                            $sender->sendMessage($this->plugin->formatMessage("Nie możesz zwolnić siebie"));
                            return true;
                        }
                        if (!$this->plugin->isOfficer($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Ten gracz nie jest oficerem"));
                            return true;
                        }
                        $factionName = $this->plugin->getPlayerFaction($player);
                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
                        $stmt->bindValue(":player", $args[1]);
                        $stmt->bindValue(":faction", $factionName);
                        $stmt->bindValue(":rank", "Member");
                        $result = $stmt->execute();
                        $player = $this->plugin->getServer()->getPlayerExact($args[1]);
                        $sender->sendMessage($this->plugin->formatMessage("$args[1] został zwolniony", true));
                        if ($player instanceof Player) {
                            $player->sendMessage($this->plugin->formatMessage("Zostałeś zwolniony w gildi $factionName!", true));
                            $this->plugin->updateTag($this->plugin->getServer()->getPlayerExact($args[1])->getName());
                            return true;
                        }
                    }

                    /////////////////////////////// KICK ///////////////////////////////

                   if ($args[0] == "wywal") {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Usage: /g wywal <gracz>"));
                            return true;
                        }
                        if ($this->plugin->isInFaction($sender->getName()) == false) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być w gildi aby tego użyć"));
                            return true;
                        }
                        if ($this->plugin->isLeader($player) == false) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby tego uzyć"));
                            return true;
                        }
                        if ($this->plugin->getPlayerFaction($player) != $this->plugin->getPlayerFaction($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Gracz nie należy do gildi"));
                            return true;
                        }
                        if ($args[1] == $sender->getName()) {
                            $sender->sendMessage($this->plugin->formatMessage("Nie możesz wyrzucić siebie."));
                            return true;
                        }
                        $kicked = $this->plugin->getServer()->getPlayerExact($args[1]);
                        $factionName = $this->plugin->getPlayerFaction($player);
                        $this->plugin->db->query("DELETE FROM master WHERE player='$args[1]';");
                        $sender->sendMessage($this->plugin->formatMessage("Wyrzucono gracza $args[1]", true));

                        if ($kicked instanceof Player) {
                            $kicked->sendMessage($this->plugin->formatMessage("Zostałeś wywalony z > $factionName",§bOszusci jedni true));
                            $this->plugin->updateTag($this->plugin->getServer()->getPlayerExact($args[1])->getName());
                            return true;
                        }
                    }
					

                    /////////////////////////////// INFO ///////////////////////////////

                    if (strtolower($args[0]) == 'info') {
                        if (isset($args[1])) {
                            if (!(ctype_alnum($args[1])) | !($this->plugin->factionExists($args[1]))) {
                                $sender->sendMessage($this->plugin->formatMessage("Upewnij sie, ze poprawnie napisales. LICZA SIE MALE I DUZE LITERY!"));
                                return true;
                            }
                            $faction = $args[1];
                            $result = $this->plugin->db->query("SELECT * FROM motd WHERE faction='$faction';");
                            $array = $result->fetchArray(SQLITE3_ASSOC);
                            $power = $this->plugin->getFactionPower($faction);
                            $money = $this->plugin->getFactionMoney($faction);
                            $message = $array["message"];
                            $leader = $this->plugin->getLeader($faction);
                            $numPlayers = $this->plugin->getNumberOfPlayers($faction);
							$sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "§8[§7------- §c[Gildie] §7-------§8]");
							$sender->sendMessage("§7Gildia:§a $faction ");
							$sender->sendMessage("§c* §7Lider:§a $leader");
							$sender->sendMessage("§c* §7Liczba Czlonkow:§a $numPlayers");
							$sender->sendMessage("§c* §7Punkty:§a $power");
							$sender->sendMessage("§c* §7OPIS:§a $message");
							$sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "§8[§7------- §c[Gildie] §7-------§8]");
                        } else {
                            if (!$this->plugin->isInFaction($player)) {
                                $sender->sendMessage($this->plugin->formatMessage("Musisz mieć gildie aby to wykonać!"));
                                return true;
                            }
                            $faction = $this->plugin->getPlayerFaction(($sender->getName()));
                            $result = $this->plugin->db->query("SELECT * FROM motd WHERE faction='$faction';");
                            $array = $result->fetchArray(SQLITE3_ASSOC);
                            $power = $this->plugin->getFactionPower($faction);
                            $money = $this->plugin->getFactionMoney($faction);
                            $message = $array["message"];
                            $leader = $this->plugin->getLeader($faction);
                            $numPlayers = $this->plugin->getNumberOfPlayers($faction);
							$sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "§8[§7------- §c[Gildie] §7-------§8]");
							$sender->sendMessage(TextFormat::BOLD . TextFormat::GOLD . "§7Gildia: §c$faction §6");
							$sender->sendMessage("§c* §7Lider:§c $leader");
							$sender->sendMessage("§c* §7Liczba Czlonkow:§c $numPlayers");
							$sender->sendMessage("§c* §7Punkty:§c $power");
							$sender->sendMessage("§c* §7OPIS:§c $message");
							$sender->sendMessage(TextFormat::BOLD . TextFormat::GRAY . "§8[§7------- §c[Gildie] §7-------§8]");
                    return true;
                        }
                    }
/*Help Commands*/
 					if(strtolower($args[0]) == "pomoc") {
						if(!isset($args[1]) || $args[1] == 1) {
							$sender->sendMessage(TextFormat::BLUE . "§8[ §7----------- §a[Gildie] §7----------- §8]" . TextFormat::GREEN . "\n§a /g autor §7- Wysyla informacje o pluginie\n§a /g akceptuj §7- Akcpetuje zproszenie do gildi\n§a /g teren §7- Zaklada nowy teren gildi\n§a /g zaloz <nazwa> §7- Zaklada gildie\n§a /g usun §7- Usuwa gildie\n§a /g zwolnij <gracz> §7- Degraduje gracza\n§a /g odrzuc §7- Odrzuca zaproszenie do gildi\n§8[ §7----------- §a[Gildie] §7----------- §8]");
							return true;
						}
						if($args[1] == 2) {
							$sender->sendMessage(TextFormat::BLUE . "§8[ §7----------- §a[Gildie] §7----------- §8]" . TextFormat::GREEN . "\n§a /g baza §7- Teleportuje do domu gildi\n§a /g pomoc <strona> §7- Komendy gildi\n§a /g info §7- Informacje o gildi\n§a /g info <gildia> §7- Informajce o innej gildi\n§a /g dodaj <gracz> §7- zaprasza gracza do gildi\n§a /g wyrzuc <gracz> §7- wyrzuca gracza z gildi\n§a /g lider <gracz> §7- Oddaje lidera innemu graczowi\n§a /g opusc §7- Opuszcza gildie\n§8[ §7----------- §a[Gildie] §7----------- §8]");
							return true;
						}
						if($args[1] == 3) {
						    $sender->sendMessage(TextFormat::BLUE . "§8[ §7----------- §a[Gildie] §7----------- §8]" . TextFormat::GREEN . "\n§a /g awansuj <gracz> §7- awansuje gracza do oficera\n§a /g ustawbaze §7- ustawia dom gildi\n§a /g usunteren §7- usuwa teren\n§a /g usunbaze\ §7- usuwa dom gildi\n§a /g # <wiadomosc> §7- Wysyla wiadomosc do graczy w gildi\n§a /g top §7- TOP10 gildi\n§a /g sojusz <gildia> §7- Wysyla prosbe o sojusz\n§a /g sojuszakceptuj §7- Akceptuje sojusz\n\n§a /g sojuszodrzuc §7- Odrzuca prosbe o sojusz\n§8[ §7----------- §a[Gildie] §7----------- §8]");
							return true;	
						} 
						if($args[1] == 4) {
						$sender->sendMessage(TextFormat::BLUE . "§8[ §7----------- §a[Gildie] §7----------- §8]" . TextFormat::GREEN . "\n§a /g zerwijsojusz <gildia> §7- zrywa sojusz\n§a /g sojusze <gildia> §7- wysyla sojusze konkretnej gildi\n§a /g sojusze §7- wysyla sojusze twojej gildi\n§a /g listaz <gildia> §7- wysyla liste czlonkow konkretnej gildi\n§a /g lista §7- wysyla liste czlonkow twojej gildi\n§a /g sprawdz §7- Wysyla informacje czy teren jest zajety\n§a /g opis §7- ustawia opis gildi\n§8[ §7----------- §a[Gildie] §7----------- §8]");
							return true;	
                    }
                }
				}



					/////////////////////////////// CLAIM ///////////////////////////////
					
					if(strtolower($args[0]) == 'teren') {
						if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz posiadać gildie aby założyć teren"));
							return true;
						}
                        if($this->plugin->prefs->get("OfficersCanClaim")){
                            if(!$this->plugin->isLeader($player) || !$this->plugin->isOfficer($player)) {
							    $sender->sendMessage($this->plugin->formatMessage("Tylko liderzy oraz oficerzy mogą zakładać teren!"));
							    return true;
						    }
                        } else {
                            if(!$this->plugin->isLeader($player)) {
							    $sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby to wykonać"));
							    return true;
						    }
                        }
                        if (!$this->plugin->isLeader($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby to wykonać"));
                            return true;
                        }
                        if (!in_array($sender->getPlayer()->getLevel()->getName(), $this->plugin->prefs->get("ClaimWorlds"))) {
                            $sender->sendMessage($this->plugin->formatMessage("Działki można robić tylko w: " . implode(" ", $this->plugin->prefs->get("ClaimWorlds"))));
                            return true;
                        }
                        
						if($this->plugin->inOwnPlot($sender)) {
							$sender->sendMessage($this->plugin->formatMessage("W tym miejscu jest twoj lub inny teren!"));
							return true;
						}
						$faction = $this->plugin->getPlayerFaction($sender->getPlayer()->getName());
                        if($this->plugin->getNumberOfPlayers($faction) < $this->plugin->prefs->get("PlayersNeededInFactionToClaimAPlot")){
                           
                           $needed_players =  $this->plugin->prefs->get("PlayersNeededInFactionToClaimAPlot") - 
                                               $this->plugin->getNumberOfPlayers($faction);
                           $sender->sendMessage($this->plugin->formatMessage("§bYou need §e$needed_players §bmore players to claim"));
				           return true;
                        }
                        if($this->plugin->getFactionPower($faction) < $this->plugin->prefs->get("PowerNeededToClaimAPlot")){
                            $needed_power = $this->plugin->prefs->get("PowerNeededToClaimAPlot");
                            $faction_power = $this->plugin->getFactionPower($faction);
							$sender->sendMessage($this->plugin->formatMessage("§3Your guilds doesn't have enough power to claim"));
							$sender->sendMessage($this->plugin->formatMessage("§e"."$needed_power" . " §3power is required. Your guilds only has §a$faction_power §3power."));
                            return true;
                        }
						$x = floor($sender->getX());
						$y = floor($sender->getY());
						$z = floor($sender->getZ());
						if($this->plugin->drawPlot($sender, $faction, $x, $y, $z, $sender->getPlayer()->getLevel(), $this->plugin->prefs->get("PlotSize")) == false) {
                            
							return true;
						}
                        
						$sender->sendMessage($this->plugin->formatMessage("Sprawdzanie twoich koordynatów...", true));
                        $plot_size = $this->plugin->prefs->get("PlotSize");
                        $faction_power = $this->plugin->getFactionPower($faction);
						$sender->sendMessage($this->plugin->formatMessage("Teren został założony!", true));
					}
					 					
					/////////////////////////////// UNCLAIM ///////////////////////////////
					
					if(strtolower($args[0]) == "usunteren") {
						if(!$this->plugin->isLeader($sender->getName())) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby tego uźyć."));
							return true;
						}
						$faction = $this->plugin->getPlayerFaction($sender->getName());
						$this->plugin->db->query("DELETE FROM plots WHERE faction='$faction';");
						$sender->sendMessage($this->plugin->formatMessage("Działka skasowana.", true));
					}
					
					/////////////////////////////// SETHOME ///////////////////////////////
					
					if(strtolower($args[0] == "ustawbaze")) {
						if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz być w gildi aby ustawić dom."));
						}
						if(!$this->plugin->isLeader($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby ustawić dom."));
							return true;
						}
						$factionName = $this->plugin->getPlayerFaction($sender->getName());
						$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO home (faction, x, y, z) VALUES (:faction, :x, :y, :z);");
						$stmt->bindValue(":faction", $factionName);
						$stmt->bindValue(":x", $sender->getX());
						$stmt->bindValue(":y", $sender->getY());
						$stmt->bindValue(":z", $sender->getZ());
						$result = $stmt->execute();
						$sender->sendMessage($this->plugin->formatMessage("Dom ustawiony!", true));
					}
					
					/////////////////////////////// UNSETHOME ///////////////////////////////
						
					if(strtolower($args[0] == "usunbaze")) {
						if(!$this->plugin->isInFaction($player)) {
							$player->sendMessage($this->plugin->formatMessage("Musisz być w gildi aby usunąć dom."));
						}
						if(!$this->plugin->isLeader($player)) {
							$player->sendMessage($this->plugin->formatMessage("Musisz być liderem aby usunąć dom"));
							return true;
						}
						$faction = $this->plugin->getPlayerFaction($sender->getName());
						$this->plugin->db->query("DELETE FROM home WHERE faction = '$faction';");
						$sender->sendMessage($this->plugin->formatMessage("Home unset!", true));
					}
					
					
					/////////////////////////////// HOME ///////////////////////////////
						
					if(strtolower($args[0] == "baza")) {
						if(!$this->plugin->isInFaction($player)) {
							$player->sendMessage($this->plugin->formatMessage("Musisz być w gildi."));
						}
						$faction = $this->plugin->getPlayerFaction($sender->getName());
						$result = $this->plugin->db->query("SELECT * FROM home WHERE faction = '$faction';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(!empty($array)) {
							$sender->getPlayer()->teleport(new Vector3($array['x'], $array['y'], $array['z']));
							$sender->sendMessage($this->plugin->formatMessage("Teleportowano.", true));
							return true;
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Dom nie jest ustawiony."));
							}
						}
						
					/////////////////////////////// TOP10 ///////////////////////////////
                    //TOP10 Leaderboards
                    if (strtolower($args[0]) == 'top') {
                        $this->plugin->sendListOfTop10FactionsTo($sender);
						$sender->sendMessage("§8[§7---------- §8[§cTOP GILDIE§8]§7 ----------§8]");
                    }
					
                    //Add Guilds Points
                    if (strtolower($args[0]) == 'dodajpunkty') {
                        if (!isset($args[1]) or ! isset($args[2])) {
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g dodajpunkty <gildia> <ilość>"));
                            return true;
                        }
                        if (!$this->plugin->factionExists($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Taka gildia nie istnieje"));
                            return true;
                        }
                        if (!($sender->isOp())) {
                            $sender->sendMessage($this->plugin->formatMessage("You don't have permission."));
                            return true;
                        }
                        $this->plugin->addFactionPower($args[1], $args[2]);
                        $sender->sendMessage($this->plugin->formatMessage("Dodano $args[2] punktow gildi: $args[1]", true));
                    }
					
					/////////////////////////////// OPUSUN OPUSUNTEREN ///////////////////////////////
					                    if(strtolower($args[0] == "usung")){
                        if(!isset($args[1])){
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g usung"));
                            return true;
                        }
                        if(!$this->plugin->factionExists($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("§cTaka gildia nie istnieje"));
                            return true;
						}
                        if(!($sender->isOp())) {
							$sender->sendMessage("§cYou don't have permission to use this");
                            return true;
						}
				        $sender->sendMessage($this->plugin->formatMessage("§cTeren gildi §a$args[1]§c został usunięty"));
                        $this->plugin->db->query("DELETE FROM plots WHERE faction='$args[1]';");
                        
                    }

                    if (strtolower($args[0]) == 'usun') {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g opusun <nazwa>"));
                            return true;
                        }
                        if (!$this->plugin->factionExists($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Taka gildia nie istnieje."));
                            return true;
                        }
                        if (!($sender->isOp())) {
                            $sender->sendMessage("§cYou don't have permission to use this!");
                            return true;
                        }
                        $this->plugin->db->query("DELETE FROM master WHERE faction='$args[1]';");
                        $this->plugin->db->query("DELETE FROM plots WHERE faction='$args[1]';");
                        $this->plugin->db->query("DELETE FROM allies WHERE faction1='$args[1]';");
                        $this->plugin->db->query("DELETE FROM allies WHERE faction2='$args[1]';");
                        $this->plugin->db->query("DELETE FROM strength WHERE faction='$args[1]';");
                        $this->plugin->db->query("DELETE FROM motd WHERE faction='$args[1]';");
                        $this->plugin->db->query("DELETE FROM home WHERE faction='$args[1]';");
                        $sender->sendMessage($this->plugin->formatMessage("Gildia oraz jej dzialki zostały usunięte", true));
                    }

                    /////////////////////////////// DESCRIPTION ///////////////////////////////

                    if (strtolower($args[0]) == "opis") {
                        if ($this->plugin->isInFaction($sender->getName()) == false) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być w gildi aby to wykonać"));
                            return true;
                        }
                        if ($this->plugin->isLeader($player) == false) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby to wykonać"));
                            return true;
                        }
                        $sender->sendMessage($this->plugin->formatMessage("Wpisz opis do chatu. Nikt tego nie zobaczy!", true));
                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO motdrcv (player, timestamp) VALUES (:player, :timestamp);");
                        $stmt->bindValue(":player", $sender->getName());
                        $stmt->bindValue(":timestamp", time());
                        $result = $stmt->execute();
                    }

                    /////////////////////////////// ACCEPT ///////////////////////////////

 					if(strtolower($args[0]) == "akceptuj") {
						$player = $sender->getName();
						$lowercaseName = strtolower($player);
						$result = $this->plugin->db->query("SELECT * FROM confirm WHERE player='$lowercaseName';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(empty($array) == true) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz zaproszeń!"));
							return true;
						}
						else {
						$invitedTime = $array["timestamp"];
						$currentTime = time();
						if( ($currentTime - $invitedTime) <= 60 ) { //This should be configurable
							$faction = $array["faction"];
							$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
							$stmt->bindValue(":player", strtolower($player));
							$stmt->bindValue(":faction", $faction);
							$stmt->bindValue(":rank", "Member");
							$result = $stmt->execute();
							$this->plugin->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
							$sender->sendMessage($this->plugin->formatMessage("Dołaczyłeś do gildi >$faction!", true));
							$this->plugin->getServer()->getPlayerExact($array["invitedby"])->sendMessage($this->plugin->formatMessage("$player gracz dołaczył do gildi!", true));
						}
						else {
							$sender->sendMessage($this->plugin->formatMessage("Czas na przyjęcie zaproszenia minął!"));
							$this->plugin->db->query("DELETE * FROM confirm WHERE player='$player';");
						}
					}
					}
                    /////////////////////////////// DENY ///////////////////////////////

					if(strtolower($args[0]) == "akceptuj") {
						$player = $sender->getName();
						$lowercaseName = strtolower($player);
						$result = $this->plugin->db->query("SELECT * FROM confirm WHERE player='$lowercaseName';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(empty($array) == true) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz zaproszeń!"));
							return true;
						}
						$invitedTime = $array["timestamp"];
						$currentTime = time();
						if( ($currentTime - $invitedTime) <= 60 ) { //This should be configurable
							$faction = $array["faction"];
							$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
							$stmt->bindValue(":player", strtolower($player));
							$stmt->bindValue(":faction", $faction);
							$stmt->bindValue(":rank", "Member");
							$result = $stmt->execute();
							$this->plugin->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
							$sender->sendMessage($this->plugin->formatMessage("Dołaczyłeś do gildi $faction!", true));
							$this->plugin->getServer()->getPlayerExact($array["invitedby"])->sendMessage($this->plugin->formatMessage("$player gracz dołaczył do gildi! > §factionName.", true));
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Invite has timed out!"));
							$this->plugin->db->query("DELETE * FROM confirm WHERE player='$player';");
						}
					}

                    /////////////////////////////// DELETE ///////////////////////////////

                    if (strtolower($args[0]) == "usun") {
                        if ($this->plugin->isInFaction($player) == true) {
                            if ($this->plugin->isLeader($player)) {
                                $faction = $this->plugin->getPlayerFaction($player);
                                $this->plugin->db->query("DELETE FROM plots WHERE faction='$faction';");
                                $this->plugin->db->query("DELETE FROM master WHERE faction='$faction';");
                                $this->plugin->db->query("DELETE FROM allies WHERE faction1='$faction';");
                                $this->plugin->db->query("DELETE FROM allies WHERE faction2='$faction';");
                                $this->plugin->db->query("DELETE FROM strength WHERE faction='$faction';");
                                $this->plugin->db->query("DELETE FROM motd WHERE faction='$faction';");
                                $this->plugin->db->query("DELETE FROM home WHERE faction='$faction';");
                                $sender->sendMessage($this->plugin->formatMessage("Gildia została usunięta", true));
                                $this->plugin->updateTag($sender->getName());
                            } else {
                                $sender->sendMessage($this->plugin->formatMessage("Musisz być liderem aby to wykonać!"));
                            }
                        } else {
                            $sender->sendMessage($this->plugin->formatMessage("Nie posiadasz gildi!"));
                        }
                    }

                    /////////////////////////////// LEAVE ///////////////////////////////

 					if(strtolower($args[0] == "opusc")) {
						if($this->plugin->isLeader($player) == false) {
							$remove = $sender->getPlayer()->getNameTag();
							$faction = $this->plugin->getPlayerFaction($player);
							$name = $sender->getName();
							$this->plugin->db->query("DELETE FROM master WHERE player='$name';");
							$sender->sendMessage($this->plugin->formatMessage("Odszedłeś z $faction", true));
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Musisz usunąć gildie lub przekazać lidera!"));
						}
					}

					/////////////////////////////// CZAT ////////////////////////////////
					                    if (strtolower($args[0] == "#")) {
                        if (!($this->plugin->isInFaction($player))) {
                            $sender->sendMessage($this->plugin->formatMessage("Musisz być w gildi, aby pisać w czacie gildyjnym!"));
                            return true;
                        }
                        $r = count($args);
                        $row = array();
                        $rank = "Czlonek";
                        $f = $this->plugin->getPlayerFaction($player);
                        if ($this->plugin->isOfficer($player)) {
                            $rank = "Oficer";
                        } else if ($this->plugin->isLeader($player)) {
                            $rank = "Lider";
                        }
                        $message = " ";
                        for ($i = 0; $i < $r - 1; $i = $i + 1) {
                            $message = $message . $args[$i + 1] . " ";
                        }
                        $result = $this->plugin->db->query("SELECT * FROM master WHERE faction='$f';");
                        for ($i = 0; $resultArr = $result->fetchArray(SQLITE3_ASSOC); $i = $i + 1) {
                            $row[$i]['player'] = $resultArr['player'];
                            $p = $this->plugin->getServer()->getPlayerExact($row[$i]['player']);
                            if ($p instanceof Player) {
                                $p->sendMessage("§f• §8[§c" . $rank . "§8]§e $player:§b" . $message . "§f•");
  
                            }
                        }
                    }
                    ////////////////////////////// ALLY SYSTEM ////////////////////////////////
					
                    if (strtolower($args[0] == "sojusz")) {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Użyj: /g sojusz <gidlia>"));
                            return true;
                        }
                        if (!$this->plugin->isInFaction($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być w gildi aby to wykonać"));
                            return true;
                        }
                        if (!$this->plugin->isLeader($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być liderem aby to wykonać"));
                            return true;
                        }
                        if (!$this->plugin->factionExists($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Taka gildia nie istnieje"));
                            return true;
                        }
                        if ($this->plugin->getPlayerFaction($player) == $args[1]) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Nie możesz wysłać prośby o sojusz do swojej gildi!"));
                            return true;
                        }
                        if ($this->plugin->areAllies($this->plugin->getPlayerFaction($player), $args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Twoja gildia zawarła już sojusz z $args[1]"));
                            return true;
                        }
                        $fac = $this->plugin->getPlayerFaction($player);
                        $leader = $this->plugin->getServer()->getPlayerExact($this->plugin->getLeader($args[1]));
                        $this->plugin->updateAllies($fac);
                        $this->plugin->updateAllies($args[1]);
                        if (!($leader instanceof Player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Lider wybranej gildi jest offline!"));
                            return true;
                        }
                        if ($this->plugin->getAlliesCount($args[1]) >= $this->plugin->getAlliesLimit()) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Twoja gidlia osiągneła limit sojuszy!", false));
                            return true;
                        }
                        if ($this->plugin->getAlliesCount($fac) >= $this->plugin->getAlliesLimit()) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Twoja gidlia osiągneła limit sojuszy!", false));
                            return true;
                        }
                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO alliance (player, faction, requestedby, timestamp) VALUES (:player, :faction, :requestedby, :timestamp);");
                        $stmt->bindValue(":player", $leader->getName());
                        $stmt->bindValue(":faction", $args[1]);
                        $stmt->bindValue(":requestedby", $sender->getName());
                        $stmt->bindValue(":timestamp", time());
                        $result = $stmt->execute();
                        $sender->sendMessage($this->plugin->formatMessage("§cWysłano prośbe o sojusz do gildi $args[1]! Poczekaj na odpowiedz...", true));
                        $leader->sendMessage($this->plugin->formatMessage("§cLider gidli $fac wysyła prośbe o sojusz. Wpisz /g sojuszakceptuj aby zaakceptować lub /g sojsuzodrzuc aby odrzucic...", true));
                    }
                    if (strtolower($args[0] == "zerwijsojusz")) {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Użyj: /g zerwijsojusz <gildia>"));
                            return true;
                        }
                        if (!$this->plugin->isInFaction($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być w gildi aby to wykonać"));
                            return true;
                        }
                        if (!$this->plugin->isLeader($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być liderem gildi aby to wykonać"));
                            return true;
                        }
                        if (!$this->plugin->factionExists($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Taka gidlia nie istnieje!"));
                            return true;
                        }
                        if ($this->plugin->getPlayerFaction($player) == $args[1]) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Nie możesz zerwać sojuszu sam ze sobą!"));
                            return true;
                        }
                        if (!$this->plugin->areAllies($this->plugin->getPlayerFaction($player), $args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Twoja gildia nie posiadała sojuszu z $args[1]"));
                            return true;
                        }
                        $fac = $this->plugin->getPlayerFaction($player);
                        $leader = $this->plugin->getServer()->getPlayerExact($this->plugin->getLeader($args[1]));
                        $this->plugin->deleteAllies($fac, $args[1]);
                        $this->plugin->deleteAllies($args[1], $fac);
                        $this->plugin->subtractFactionPower($fac, $this->plugin->prefs->get("PowerGainedPerAlly"));
                        $this->plugin->subtractFactionPower($args[1], $this->plugin->prefs->get("PowerGainedPerAlly"));
                        $this->plugin->updateAllies($fac);
                        $this->plugin->updateAllies($args[1]);
                        $sender->sendMessage($this->plugin->formatMessage("§7Twoja gidlia ( $fac ) zerwała sojusz z gildia $args[1]", true));
                        if ($leader instanceof Player) {
                            $leader->sendMessage($this->plugin->formatMessage("§7Lider gildi $fac zerwał sojsuz z twoją gidlią ( $args[1] )", false));
                        }
                    }
                    if (strtolower($args[0] == "sojusze")) {
                        if (!isset($args[1])) {
                            if (!$this->plugin->isInFaction($player)) {
                                $sender->sendMessage($this->plugin->formatMessage("§7Musisz być w gildi aby to wykonać"));
                                return true;
                            }
                            $this->plugin->updateAllies($this->plugin->getPlayerFaction($player));
                            $this->plugin->getAllAllies($sender, $this->plugin->getPlayerFaction($player));
                        } else {
                            if (!$this->plugin->factionExists($args[1])) {
                                $sender->sendMessage($this->plugin->formatMessage("§7Taka gildia nie istnieje"));
                                return true;
                            }
                            $this->plugin->updateAllies($args[1]);
                            $this->plugin->getAllAllies($sender, $args[1]);
                        }
                    }
                    if (strtolower($args[0] == "sojuszakceptuj")) {
                        if (!$this->plugin->isInFaction($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być w gidli aby to wykonać"));
                            return true;
                        }
                        if (!$this->plugin->isLeader($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być liderem aby to wykonać"));
                            return true;
                        }
                        $lowercaseName = ($player);
                        $result = $this->plugin->db->query("SELECT * FROM alliance WHERE player='$lowercaseName';");
                        $array = $result->fetchArray(SQLITE3_ASSOC);
                        if (empty($array) == true) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Twoja gildia nie posiada próśb o sojusz!"));
                            return true;
                        }
                        $allyTime = $array["timestamp"];
                        $currentTime = time();
                        if (($currentTime - $allyTime) <= 60) { //This should be configurable
                            $requested_fac = $this->plugin->getPlayerFaction($array["requestedby"]);
                            $sender_fac = $this->plugin->getPlayerFaction($player);
                            $this->plugin->setAllies($requested_fac, $sender_fac);
                            $this->plugin->setAllies($sender_fac, $requested_fac);
                            $this->plugin->addFactionPower($sender_fac, $this->plugin->prefs->get("PowerGainedPerAlly"));
                            $this->plugin->addFactionPower($requested_fac, $this->plugin->prefs->get("PowerGainedPerAlly"));
                            $this->plugin->db->query("DELETE FROM alliance WHERE player='$lowercaseName';");
                            $this->plugin->updateAllies($requested_fac);
                            $this->plugin->updateAllies($sender_fac);
                            $sender->sendMessage($this->plugin->formatMessage("§7Twoja gildia zawarła sojusz z $requested_fac", true));
                            $this->plugin->getServer()->getPlayerExact($array["requestedby"])->sendMessage($this->plugin->formatMessage("$player z $sender_fac zaakceptował sojusz!!", true));
                        } else {
                            $sender->sendMessage($this->plugin->formatMessage("§7Czas minął"));
                            $this->plugin->db->query("DELETE * FROM alliance WHERE player='$lowercaseName';");
                        }
                    }
                    if (strtolower($args[0]) == "sojuszodrzuc") {
                        if (!$this->plugin->isInFaction($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być w gildi aby to wykonać!"));
                            return true;
                        }
                        if (!$this->plugin->isLeader($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być liderem aby to wykonać"));
                            return true;
                        }
                        $lowercaseName = ($player);
                        $result = $this->plugin->db->query("SELECT * FROM alliance WHERE player='$lowercaseName';");
                        $array = $result->fetchArray(SQLITE3_ASSOC);
                        if (empty($array) == true) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Twoja gildia nie posiada próśb o sojusz!"));
                            return true;
                        }
                        $allyTime = $array["timestamp"];
                        $currentTime = time();
                        if (($currentTime - $allyTime) <= 60) { //This should be configurable
                            $requested_fac = $this->plugin->getPlayerFaction($array["requestedby"]);
                            $sender_fac = $this->plugin->getPlayerFaction($player);
                            $this->plugin->db->query("DELETE FROM alliance WHERE player='$lowercaseName';");
                            $sender->sendMessage($this->plugin->formatMessage("§7Twoja gildia odmówila sojuszu.", true));
                            $this->plugin->getServer()->getPlayerExact($array["requestedby"])->sendMessage($this->plugin->formatMessage("$player z $sender_fac odrzucił prośbe o sojusz!"));
                        } else {
                            $sender->sendMessage($this->plugin->formatMessage("§7Czas minął"));
                            $this->plugin->db->query("DELETE * FROM alliance WHERE player='$lowercaseName';");
                        }
                    }
                   /////////////////////////////// LISTA ///////////////////////////////
				   
                    if (strtolower($args[0] == "lista")) {
                        if (!$this->plugin->isInFaction($player)) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Musisz być w gidli aby to wykonać"));
                            return true;
                        }
						$faction = $this->plugin->getPlayerFaction(($sender->getName()));
                        $sender->sendMessage("§7Czlonkowie Gildi:§c $faction");
                        $sender->sendMessage("§7Lider:");
                        $this->plugin->getPlayersInFactionByRank($sender, $this->plugin->getPlayerFaction($player), "Leader");
                        $sender->sendMessage("§7Oficerzy:");
                        $this->plugin->getPlayersInFactionByRank($sender, $this->plugin->getPlayerFaction($player), "Officer");
                        $sender->sendMessage("§7Czlonkowie:");
                        $this->plugin->getPlayersInFactionByRank($sender, $this->plugin->getPlayerFaction($player), "Member");
                    }
                   /////////////////////////////// LISTA ///////////////////////////////
                    if (strtolower($args[0] == "listaz")) {
                        if (!isset($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("Użyj: /g listaz <gildia>"));
                            return true;
                        }
                        if (!$this->plugin->factionExists($args[1])) {
                            $sender->sendMessage($this->plugin->formatMessage("§7Taka gildia nie istnieje!"));
                            return true;
                        }
                        $faction = $args[1];
                        $sender->sendMessage("§7Czlonkowie Gildi:§c $faction");
                        $sender->sendMessage("§7Lider:");
                        $this->plugin->getPlayersInFactionByRank($sender, $args[1], "Leader");
						$sender->sendMessage("§7Oficerzy:");
                        $this->plugin->getPlayersInFactionByRank($sender, $args[1], "Officer");
                        $sender->sendMessage("§7Czlonkowie:");
                        $this->plugin->getPlayersInFactionByRank($sender, $args[1], "Member");
                    }
                   /////////////////////////////// SPRAWDZ ///////////////////////////////
				   
					if(strtolower($args[0]) == 'sprawdz'){
                        $x = floor($sender->getX());
						$y = floor($sender->getY());
						$z = floor($sender->getZ());
                        $fac = $this->plugin->factionFromPoint($x,$z);
                        $power = $this->plugin->getFactionPower($fac);
                        if(!$this->plugin->isInPlot($sender)){
                            $sender->sendMessage($this->plugin->formatMessage("§7Ten teren jest wolny. Jeżeli chcesz wpisz /g teren aby go zająć!", true));
							return true;
                        }
                        $sender->sendMessage($this->plugin->formatMessage("§7Ten teren jest zajęty przez gildie:§a $fac §cktóra posiada §a$power §7punktów gildi!"));
                    }
                    
					
                    /////////////////////////////// ABOUT ///////////////////////////////

                    if (strtolower($args[0] == 'autor')) {
                        $sender->sendMessage(TextFormat::GREEN . "§8[§7===========§8[§aGildie§8]§7===========§8]");
                        $sender->sendMessage(TextFormat::GREEN . "§aGildie by XFanta 1337");
                        $sender->sendMessage(TextFormat::GREEN . "§a Błendy zglos na kanale XFanta 1337");
                        $sender->sendMessage(TextFormat::GREEN . "§8[§7===========§8[§aGildie§8]§7===========§8]");
                    }
                    //Thanks To The original authors Tethered_
                    //Thank To The Supporter
                    //Big Thanks To NeuroBinds Project Corporation For Helping 64% Of The Code!
                }
            }
	}
        }
