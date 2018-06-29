<?php
include_once 'constants.php';
$inteaching = (substr($CWD,0,9)=="/teaching");
echo
'<nav id="navbar">
  <ul>
  <li><a href="' .$BASEPATH. '/">Home</a></li>
  <li><a href="' .$BASEPATH. '/aboutme.php">About Me</a></li>
  <li><a href="' .$BASEPATH. '/research">Research</a></li>
  <li><a href="' .$BASEPATH. '/teaching">Teaching</a>';
if ($inteaching) {
echo "\n".'  <ul><li><a href="' .$BASEPATH. '/teaching/teachingArchive.php">Archive</a></li></ul>'."\n";
}
echo'  </li>
  <li><a href="' .$BASEPATH. '/links.php">Links</a></li>
  </ul>
</nav>';
?>
