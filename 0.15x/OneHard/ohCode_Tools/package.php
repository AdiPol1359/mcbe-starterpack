<?php
define("DIRECTORY", getenv("WORKSPACE") ? getenv("WORKSPACE") : getcwd());
date_default_timezone_set("UTC");
print "Packaging plugin...\n";
$description = file_get_contents(DIRECTORY . "/plugin.yml");
preg_match_all("/name:(.*)/", $description, $matches);
$name = $matches[1][0];
preg_match_all("/main:(.*)/", $description, $matches);
$main = $matches[1][0];
preg_match_all("/version:(.*)/", $description, $matches);
$version = $matches[1][0];
preg_match_all("/api:(.*)/", $description, $matches);
$api = $matches[1][0];
preg_match_all("/website:(.*)/", $description, $matches);
$website = $matches[1][0];

$pharPath = DIRECTORY . "/" . $name ."_v" . $version . ".phar";
$phar = new Phar($pharPath);
$phar->setMetadata(array(
    "name" => $name,
    "version" => $version,
    "main" => $main,
    "api" => $api,
    "website" => $website,
    "creationDate" => strtotime("now")
));
$phar->setStub('<?php echo "PocketMine-MP plugin ' . $name . ' v' . $version . '\n----------------\n";if(extension_loaded("phar")){$phar = new \Phar(__FILE__);foreach($phar->getMetadata() as $key => $value){echo ucfirst($key).": ".(is_array($value) ? implode(", ", $value):$value)."\n";}}__HALT_COMPILER();');
$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->startBuffering();
$filePath = DIRECTORY;
foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filePath)) as $file) {
    $path = ltrim(str_replace(array("\\", $filePath), array("/", ""), $file), "/");
    if ($path{0} === "." || strpos($path, "/.") !== false || $path === $pharPath) {
        continue;
    }
    print "Adding $path\n";
    $phar->addFile($file, $path);
}
$phar->compressFiles(\Phar::GZ);
$phar->stopBuffering();
print "Plugin packaged.\n";
