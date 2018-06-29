<?php
// Configurations

define('BIBLIOGRAPHYSTYLE','CustomBibliographyStyle');
@define('BIBTEXBROWSER_LINK_STYLE','bib2links_custom');

@define('BIBTEXBROWSER_BIBTEX_LINKS',false);
@define('BIBTEXBROWSER_ARXIV_LINKS',true);
@define('BIBTEXBROWSER_ABSTRACT_LINKS',false);
@define('ABBRV_TYPE','none');
@define('BIBTEXBROWSER_DEFAULT_FRAME','all');

//@define('USE_INITIALS_FOR_NAMES',true);
@define('USE_FIRST_THEN_LAST',true);
@define('MARK_STUDENTS',true);

@define('BIBTEXBROWSER_LINKS_TARGET','_blank');
@define('BIBTEXBROWSER_AUTHOR_LINKS','none');

function simplifyChars($string) {
  $newstring = str_replace(array('&eacute;','&#322'),array('e','l'),$string);
  return $newstring;
}

function CustomBibliographyStyle(&$bibentry) {
  $title = $bibentry->getTitle();
  $type = $bibentry->getType();

  // title
  $title = '"'.$title.'."';
  //if ($bibentry->hasField('url')) $title = ' <a'.get_target().' href="'.$bibentry->getField('url').'">'.$title.'</a>';
  $result = $title .' ';

  // authors
  if ($bibentry->hasField('author')) {
    $authorstring = getFormattedOtherAuthorsString($bibentry);
    if ($authorstring!='') $authorstring = 'With ' . $authorstring . '. ';
    $result .= $authorstring;
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
  if ($type=="unpublished" && $bibentry->hasField("note")) {
      $booktitle = $bibentry->getField("journal");
  }
  // editor
  $editor='';
  if ($bibentry->hasField(EDITOR)) {
    $editor = $bibentry->getFormattedEditors();
  }
  // finish publication
  if ($booktitle!='') {
    if ($editor!='') $booktitle .=' ('.$editor.')';
    $result .= '<i>'.$booktitle.'</i>, ';
  }
  if ($bibentry->hasField('note')) {
    if ($bibentry->getField('note')=='Accepted') {
      $result .= ' Accepted';
    }
    if ($bibentry->getField('note')=='In preparation') {
      $result .= 'In preparation';
    }
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
  if ($publisher!='') $result .= $publisher;

  $volnumpage = '';
  if ($bibentry->hasField('volume')) {
    $volnumpage = $bibentry->getField("volume");
    if ($bibentry->hasField('number')) $volnumpage .=  '('.$bibentry->getField("number").')';
    if ($bibentry->hasField('pages')) $volnumpage .= ':';
  }
  //if ($bibentry->hasField('address')) $entry[] =  $bibentry->getField("address");
  if ($bibentry->hasField('pages')) $volnumpage .= str_replace("--", "-", $bibentry->getField("pages"));
  if ($volnumpage!='') $result .= $volnumpage.', ';

  if ($bibentry->hasField(YEAR)) $result .= $bibentry->getYear();

  //$result = implode(", ",$entry);
  if (substr($result,-2)!='. ' && substr($result,-6)!='.</i> ') $result .= '. ';

  // add the Coin URL
  $result .=  "\n".$bibentry->toCoins();

  return $result;
}


function getFormattedOtherAuthorsArray(&$bibentry) {
  $array_authors = array();

  // first we use formatAuthor
  foreach ($bibentry->getRawAuthors() as $author) {
    //list($firstname, $lastname) = splitFullName($author);
    $array_authors[]= preg_replace('/&#322;?/','&#322;',$bibentry->formatAuthor($author));
  }
  
  if (bibtexbrowser_configuration('MARK_STUDENTS')) {
    $ugarray = getUGRADs($bibentry);
    foreach ($ugarray as $ugnum) {
      $array_authors[$ugnum-1] .= '&dagger;';
    }
    $grarray = getGRADs($bibentry);
    foreach ($grarray as $grnum) {
      $array_authors[$grnum-1] .= '&Dagger;';
    }
  }

  if (BIBTEXBROWSER_AUTHOR_LINKS=='homepage') {
    foreach ($array_authors as $k => $author) {
      $array_authors[$k]=$bibentry->addHomepageLink($author);
    }
  }

  if (BIBTEXBROWSER_AUTHOR_LINKS=='resultpage') {
    foreach ($array_authors as $k => $author) {
      $array_authors[$k]=$bibentry->addAuthorPageLink($author);
    }
  }

  $mekey = array_search( "Benjamin Reiniger", $array_authors );
  unset($array_authors[$mekey]);
  return array_values($array_authors);
  //ugh, that should've been easier.  I couldn't get array_diff to work...
}

function getFormattedOtherAuthorsString(&$bibentry) {
  return $bibentry->implodeAuthors(getFormattedOtherAuthorsArray($bibentry));
}

function bib2links_custom(&$bibentry) {
  $links = array();
  if (BIBTEXBROWSER_BIBTEX_LINKS) {
    $link = $bibentry->getBibLink();
    if ($link != '') { $links[] = $link; };
  }
  if (BIBTEXBROWSER_PDF_LINKS) {
    $link = $bibentry->getPdfLink(NULL,'web');
    if ($link != '') { $links[] = $link; };
  }
  if (BIBTEXBROWSER_DOI_LINKS) {
    $link = $bibentry->getDoiLink();
    if ($link != '') { $links[] = $link; };
  }
  if (BIBTEXBROWSER_GSID_LINKS) {
    $link = $bibentry->getGSLink();
    if ($link != '') { $links[] = $link; };
  }
  if (BIBTEXBROWSER_ARXIV_LINKS) {
    $link = getArxivLink($bibentry);
    if ($link != '') { $links[] = $link; };
  }
  if (BIBTEXBROWSER_ABSTRACT_LINKS) {
    $link = getAbstractLink($bibentry);
    if ($link != '') {$links[] = $link; };
  }
  return '<span class="bibmenu">'.implode(" ",$links).'</span>';
}

function getArxivLink(&$bibentry, $iconurl=NULL) {
  $str = $bibentry->getIconOrTxt('arXiv',$iconurl);
  if ($bibentry->hasField('arxiv')) {
    return '<a'.get_target().' href="https://arxiv.org/abs/'.$bibentry->getField('arxiv').'">'.$str.'</a>';
  }
  return '';
}

function getAbstractLink(&$bibentry, $iconurl=NULL) {
  $str = $bibentry->getIconOrTxt('abstract',$iconurl);
  $href = 'href="'.$bibentry->getURL().'"';
  if ($bibentry->hasField('abstract')) {
    return '<a class="abstract" title="abstract:'.$bibentry->getKey().'" '.$href.'>'.$str.'</a>';
  }
  return '';
}

function getUGRADs(&$bibentry) {
  if ($bibentry->hasField('ugrad')) {
    $ugstr = $bibentry->getField('ugrad');
    $ugarray = ($ugstr=='' ? array() : explode(' ',$ugstr));
    return $ugarray;
  }
}
function getGRADs(&$bibentry) {
    $grstr = $bibentry->getField('grad');
    $grarray = ($grstr=='' ? array() : explode(' ',$grstr));
    return $grarray;
}

?>
