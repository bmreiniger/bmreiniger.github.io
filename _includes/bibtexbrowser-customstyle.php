// Based on JanosBibliographyStyle

//NOT BEING USED

function CustomBibliographyStyle(&$bibentry) {
  $title = $bibentry->getTitle();
  $type = $bibentry->getType();

  $result = '';

  // title
  $title = '"' .$title. ',"';
  //if ($bibentry->hasField('url')) $title = ' <a'.get_target().' href="'.$bibentry->getField('url').'">'.$title.'</a>';
  $result = $title;

  // authors
  if ($bibentry->hasField('author')) {
    $result .= $bibentry->getFormattedAuthorsString();
  }

  // now the origin of the publication is in italic
  $booktitle = '';
  if (($type=="misc") && $bibentry->hasField("note")) {
    $booktitle = $bibentry->getField("note");
  }
  if ($type=="inproceedings" && $bibentry->hasField(BOOKTITLE)) {
      $booktitle = 'In '.$bibentry->getField(BOOKTITLE);
  }
  if ($type=="incollection" && $bibentry->hasField(BOOKTITLE)) {
      $booktitle = 'Chapter in '.$bibentry->getField(BOOKTITLE);
  }
  if ($type=="article" && $bibentry->hasField("journal")) {
      $booktitle = $bibentry->getField("journal");
  }
  // Editor
  $editor='';
  if ($bibentry->hasField(EDITOR)) {
    $editor = $bibentry->getFormattedEditors();
  }
  //finish up publication
  if ($booktitle!='') {
    if ($editor!='') $booktitle .=' ('.$editor.')';
    $result .= '<i>'.$booktitle.'</i>';
  }

  $publisher='';
  if ($type=="phdthesis") {
      $publisher = 'PhD thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="mastersthesis") {
      $publisher = 'Master\'s thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="techreport") {
      $publisher = 'Technical report';
      if ($bibentry->hasField("number")) {
        $publisher = $bibentry->getField("number");
      }
      $publisher .=', '.$bibentry->getField("institution");
  }
  if ($bibentry->hasField("publisher")) {
    $publisher = $bibentry->getField("publisher");
  }
  if ($publisher!='') $result .= '. ' . $publisher;

  if ($bibentry->hasField('volume')) $result .= $bibentry->getField("volume");
  if ($bibentry->hasField('number')) $result .=  '('.$bibentry->getField("number").')';

  //if ($bibentry->hasField('address')) $entry[] =  $bibentry->getField("address");

  if ($bibentry->hasField('pages')) $result .= str_replace("--", "-", ':'.$bibentry->getField("pages"));

  if ($bibentry->hasField(YEAR)) $result .= ', ' . $bibentry->getYear();

  //$result = implode(", ",$entry).'.';

  // add the Coin URL
  $result .=  "\n".$bibentry->toCoins();

  return $result;
}
