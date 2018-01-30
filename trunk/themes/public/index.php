<?
include_once(_ROOT_."/externals/parsedown/Parsedown.php");
$Parsedown = new Parsedown();

echo $Parsedown->text(file_get_contents(_ROOT_."/../README.md")); # prints: <p>Hello <em>Parsedown</em>!</p>
?>
