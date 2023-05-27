<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\entities\custom\GuildHeart;
use core\guilds\GuildPlayer;
use core\utils\ParticleUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use core\utils\ShapeUtil;
use core\Main;
use core\utils\SkinUtil;
use core\utils\SoundUtil;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Server;

class CreateCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("create", "", false, false, ["zaloz"]);

        $parameters = [
            0 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false),
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args) || !isset($args[1])){
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["tag"], ["nazwa"]]));
            return;
        }

        $tag = $args[0];
        array_shift($args);
        $name = implode(" ",$args);

        if(strlen($tag) < 2) {
            $sender->sendMessage(MessageUtil::format("Tag gildii jest za krotki! musi sie skladac z conajmniej §e2 §r§7znakow!"));
            return;
        }

        if(strlen($tag) > 4) {
            $sender->sendMessage(MessageUtil::format("Tag gildii jest za dlugi! moze wynosic maksymalnie §e4 §r§7znaki"));
            return;
        }

        if(!ctype_alnum($tag)) {
            $sender->sendMessage(MessageUtil::format("Tag gildii moze zawierac tylko litery i cyfry"));
            return;
        }

        if(strlen($name) > 30) {
            $sender->sendMessage(MessageUtil::format("Nazwa gildii jest za dluga! moze wynosic maksymalnie §e30 §r§7znakow"));
            return;
        }

        if($tag == "burak") {
            $sender->fxPs();
        }

        if(Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName()) !== null) {
            $sender->sendMessage(MessageUtil::format("Znajdujesz sie juz w jednej gildii!"));
            return;
        }

        if(Main::getInstance()->getGuildManager()->getGuild($tag) !== null) {
            $sender->sendMessage(MessageUtil::format("Gildia o takim tagu juz istnieje!"));
            return;
        }

        if(Main::getInstance()->getGuildManager()->getGuildFromPos(($pos = $sender->getPosition())) !== null) {
            $sender->sendMessage(MessageUtil::format("Znajdujesz sie na terenie cudzej gildii!"));
            return;
        }

        if(($guild = Main::getInstance()->getGuildManager()->getClosestGuild($pos)) !== null) {

            $guildHeartPos = $guild->getHeartSpawn();

            if(sqrt(pow($pos->x - $guildHeartPos->x, 2) + pow($pos->z - $guildHeartPos->z, 2)) < (Settings::$MAX_GUILD_SIZE + 100)) {
                $sender->sendMessage(MessageUtil::format("Znajdujesz sie zbyt blisko innej gildii!"));
                return;
            }
        }

        if(abs($sender->getPosition()->x) >= (Settings::$BORDER_DATA["border"] - Settings::$BORDER_GUILD_PROTECTION) || abs($sender->getPosition()->z) >= (Settings::$BORDER_DATA["border"] - Settings::$BORDER_GUILD_PROTECTION)) {
            $sender->sendMessage(MessageUtil::format("Gildie mozna zakladac §e".Settings::$BORDER_GUILD_PROTECTION." §7kratek od borderu!"));
            return;
        }

        $spawn = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn();

        if(sqrt(pow($pos->x - $spawn->x, 2) + pow($pos->z - $spawn->z, 2)) < Settings::$SPAWN_PROTECT) {
            $sender->sendMessage(MessageUtil::format("Gildie mozna zakladac §e".Settings::$SPAWN_PROTECT." §7kratek spawna!"));
            return;
        }

        if(!$sender->isCreative()) {
            foreach(Settings::$GUILD_ITEMS as $key => $item) {
                if(!$sender->getInventory()->contains($item)) {
                    $sender->sendMessage(MessageUtil::format("Nie posiadasz wszystkich itemow na gildie!"));
                    return;
                }
            }
        }

        if(!$sender->isCreative()) {
            foreach(Settings::$GUILD_ITEMS as $key => $item)
                $sender->getInventory()->removeItem($item);
        }

        $heartPosition = Position::fromObject(new Vector3($sender->getPosition()->round()->x, 30, $sender->getPosition()->round()->z), $sender->getWorld());

        $senders = [];

        $senders[$sender->getName()] = new GuildPlayer($sender->getName(), GuildPlayer::LEADER, $tag);
        $senders[$sender->getName()]->setAllSettings(true);

        Main::getInstance()->getGuildManager()->createGuild($tag, $name, $heartPosition, $senders);

        $message = "";

        if(($count = Main::getInstance()->getGuildManager()->getGuildsCount()) < 4) {
            $message = match ($count) {
                1 => "Pierwsza gildia na serwerze ",
                2 => "Druga gildia na serwerze ",
                3 => "Trzcia gildia na serwerze ",
            };
        }

        foreach($sender->getServer()->getOnlinePlayers() as $onlinePlayer) {
            if($message !== "")
                $onlinePlayer->sendTitle("§l§8[§e" . $tag . "§8]", "§e".$message . "§7!");

            $onlinePlayer->sendMessage(MessageUtil::format(($message !== "" ? "§7".$message."§8" : "")."§8[§e".$tag."§8] §7- §e".$name." §7zalozona przez §e".$sender->getName()));
            SoundUtil::addSound([$onlinePlayer], $onlinePlayer->getPosition(), "ambient.weather.lightning.impact");
        }

        ShapeUtil::createGuildShape($heartPosition);

        $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/default/guildHeart.png");
        $nbtPosition = clone ($heartPosition)->add(0.5, 1.25, 0.5);
        $nbt = CompoundTag::create()->setString("guild", $tag);

        $nbtSpawn = new GuildHeart(new Location($nbtPosition->x, $nbtPosition->y, $nbtPosition->z, $heartPosition->getWorld(), 180, 0), new Skin("custom", $skin, ""), $nbt);

        ParticleUtil::sendTotem($sender);
        ParticleUtil::spawnFireworkAt([], [[ParticleUtil::TYPE_SMALL_SPHERE, ParticleUtil::COLOR_GOLD], [ParticleUtil::TYPE_SMALL_SPHERE, ParticleUtil::COLOR_YELLOW]], $sender->getPosition());

        $nbtSpawn->spawnToAll();

        $sender->teleport($heartPosition);

        //NameTagPlayerManager::updatePlayersAround($sender);
    }
}