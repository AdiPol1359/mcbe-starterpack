/*
 BAN MANAGER
 */

CREATE TABLE IF NOT EXISTS bans (
    userName TEXT,
    address TEXT,
    deviceId TEXT,
    admin TEXT,
    reason TEXT,
    start INTEGER,
    end INTEGER
);

/*
 USER MANAGER
 */

CREATE TABLE IF NOT EXISTS users (
    userName TEXT,
    uuid  TEXT,
    deviceId TEXT
);

/*
 DROP
*/

CREATE TABLE IF NOT EXISTS 'drop' (
    userName TEXT,
    dropData TEXT
);

/*
 BACKPACK
*/

CREATE TABLE IF NOT EXISTS 'backpack' (
    userName     TEXT,
    backpackSize INTEGER,
    backpackData TEXT
);

/*
 STATS
*/

CREATE TABLE IF NOT EXISTS 'stats'(
    userName TEXT,
    statData TEXT
);

/*
 GROUPS
 */

CREATE TABLE IF NOT EXISTS 'groups' (
    nick       TEXT,
    groupName  TEXT,
    expiryDate TEXT
);

/*
 PERMISSIONS
 */

CREATE TABLE IF NOT EXISTS permissions(
    nick       TEXT,
    permission TEXT,
    expiryDate TEXT
);

/*
 MUTE
 */

CREATE TABLE IF NOT EXISTS mute (
    nick   TEXT,
    admin  TEXT,
    reason TEXT,
    start  INT,
    end    INT
);

/*
 TERRAINS
 */

CREATE TABLE IF NOT EXISTS terrain (
    name     TEXT,
    priority INT,
    pos1     TEXT,
    pos2     TEXT,
    settings TEXT
);

/*
 SAFE
 */

CREATE TABLE IF NOT EXISTS safe (
    nick        TEXT,
    id          TEXT,
    description TEXT,
    pattern     TEXT,
    items       TEXT
);

/*
 WARP
 */

CREATE TABLE IF NOT EXISTS warp (
    name        TEXT,
    position    TEXT
);

/*
 HOME
 */

CREATE TABLE IF NOT EXISTS home (
    nick     TEXT,
    name     TEXT,
    position TEXT
);

/*
 GUILDS
 */

CREATE TABLE IF NOT EXISTS 'guilds' (
    tag              TEXT,
    name             TEXT,
    size             INT,
    hearts           INT,
    health           INT,
    golemHealth      INT,
    base_position    TEXT,
    heart_position   TEXT,
    conquer_time     INT,
    expire_time      INT,
    tnt              INT,
    alliances        TEXT,
    treasury         TEXT,
    points           INT,
    slots            INT,
    regenerationGold INT
);

/*
 GUILDS PERMISSIONS
 */

CREATE TABLE IF NOT EXISTS 'guilds_permission' (
    nick              TEXT,
    guild             TEXT,
    rank              TEXT,
    beacon_break      INT,
    block_break       INT,
    tnt_place         INT,
    block_place       INT,
    interact_chest    INT,
    interact_furnace  INT,
    interact_beacon   INT,
    use_custom_blocks INT,
    add_player        INT,
    kick_player       INT,
    friendly_fire     INT,
    treasury          INT,
    panel             INT,
    regeneration      INT,
    teleport          INT,
    battle            INT,
    alliance          INT,
    alliance_pvp      INT,
    chest_locker      INT
);

/*
 IGNORE
*/

CREATE TABLE IF NOT EXISTS 'ignore' (
    userName TEXT,
    ignoreData TEXT
);

/*
  INCOGNITO
*/

CREATE TABLE IF NOT EXISTS incognito (
    nick TEXT,
    name INT,
    skin INT,
    tag  INT
);

/*
 WINGS
*/

CREATE TABLE IF NOT EXISTS wing (
    player TEXT,
    wing   TEXT
);

/*
 KITS
*/
CREATE TABLE IF NOT EXISTS 'kit' (
    nick TEXT,
    kits TEXT
);

/*
 OFFERS
*/

CREATE TABLE IF NOT EXISTS offers (
    owner TEXT,
    price INT,
    item  TEXT
);

/*
 BANK
*/
CREATE TABLE IF NOT EXISTS 'bank' (
    nick TEXT PRIMARY KEY COLLATE NOCASE,
    gold INT
);

/*
 SERVICES
*/

CREATE TABLE IF NOT EXISTS service (
    id        DOUBLE,
    nick      TEXT,
    service   INT,
    collected INT,
    time      INT
);

/*
 WARS
*/

CREATE TABLE IF NOT EXISTS 'wars' (
    id            INT,
    attackerGuild TEXT,
    attackedGuild TEXT,
    startTime     INT,
    endTime       INT,
    ended         INT,
    winnerGuild   TEXT
);

/*
 VILLAGERS
*/

CREATE TABLE IF NOT EXISTS villagers (
    id       TEXT,
    name     TEXT,
    items    TEXT,
    position TEXT
);

/*
 CHEST LOCKER
*/

CREATE TABLE IF NOT EXISTS chestlocker (
    id       INTEGER,
    player   TEXT,
    face     INTEGER,
    position TEXT
);

/*
 ADMIN LOGGER
*/

CREATE TABLE IF NOT EXISTS admins (
    nick      TEXT,
    spendTime INT,
    messages  INT,
    bans      INT,
    mutes     INT
);

/*
 TURBO DROP
*/

CREATE TABLE IF NOT EXISTS turbodrop (
    id      TEXT,
    founder TEXT,
    server  INT,
    expire  INT
);