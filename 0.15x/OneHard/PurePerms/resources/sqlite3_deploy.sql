/* PurePerms by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter) / UTF-8 */

CREATE TABLE IF NOT EXISTS groups(
  groupName TEXT PRIMARY KEY NOT NULL,
  isDefault INTEGER NOT NULL DEFAULT 0,
  inheritance TEXT NOT NULL,
  permissions TEXT NOT NULL
);

INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ('Guest', 1, '', '-essentials.kit,-essentials.kit.other,-pocketmine.command.me,pchat.colored.format,pchat.colored.nametag,pocketmine.command.list,pperms.command.ppinfo');
INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ('Admin', 0, 'Guest', 'essentials.gamemode,pocketmine.broadcast,pocketmine.command.gamemode,pocketmine.command.give,pocketmine.command.kick,pocketmine.command.teleport,pocketmine.command.time');
INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ('Owner', 0, 'Guest,Admin', 'essentials,pocketmine.command,pperms.command');
INSERT OR IGNORE INTO groups (groupName, isDefault, inheritance, permissions) VALUES ('OP', 0, '', '*');

CREATE TABLE IF NOT EXISTS groups_mw(
  groupName TEXT PRIMARY KEY NOT NULL,
  worldName TEXT NOT NULL,
  permissions TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS players(
  userName TEXT PRIMARY KEY NOT NULL,
  userGroup TEXT NOT NULL,
  permissions TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS players_mw(
  userName TEXT PRIMARY KEY NOT NULL,
  worldName TEXT NOT NULL,
  userGroup TEXT NOT NULL,
  permissions TEXT NOT NULL
);