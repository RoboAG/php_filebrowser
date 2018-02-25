<?php
//Lucas Gaitzsch, Peter Weissig
//2017-05-10
//v1.2

/*
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
*/
function endsWith($haystack, $needle) {
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function implodeUpTo($glue,$array,$upTo) {
    if (is_array($array)===false)
        return false;

    $res='';

    if ($upTo>count($array))
        $upTo=count($array);

    for ($i=0;$i<$upTo;$i++) {
        $res .= $array[$i];
        if ($i!==$upTo-1)
            $res .= $glue;
    }

    return $res;
}

$currentDir='';
if (isset($_GET["path"]))
    $currentDir=$_GET["path"];

if (($currentDir!='') && (substr($currentDir,-1)!=='/'))
    $currentDir=$currentDir.'/';
else if ($currentDir==='/')
    $currentDir='';

$tree = explode('/',$currentDir);
array_pop($tree);

// remove invalid and risky paths ("..", "." and "")
// <added 10.05.2017 by Peter Weissig>
$count_tree = count($tree);
for ($i=0;$i<$count_tree;$i++) {
    $temp = $tree[$i];
    if (($temp === '')  || ($temp === '.') || ($temp === '..')) {
        echo "Error due to invalid path!";
        unset($tree[$i]);
    }
     echo "<br>";
}
$tree = array_values($tree);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo ((count($tree)>0)?$tree[count($tree)-1]:basename(realpath('.')));?></title>
    <style type="text/css">
        #name {
          font-size:18px;
          margin-top:3px;
          margin-right:3px;
          text-align: right;
          position: absolute;
          top: 0;
          right: 0;
        }
    </style>
</head>

<body>
<div id="name">
  <span>L. Gaitzsch, P. Weissig</span><br>
  <span>2017-05-10</span><br>
  <span>v1.2</span>
</div>
<?php

if (is_array($tree) && (count($tree)>0)) {
    echo '<a href="index.php">['.basename(realpath('.')).']</a> &gt; ';
    for ($i=0;$i<count($tree);$i++) {
        if ($i!==count($tree)-1)
            echo '<a href="index.php?path='.implodeUpTo("/",$tree,$i+1).'/">['.$tree[$i].']</a> &gt; ';
        else
            echo "[$tree[$i]]";
    }
    echo "<br>";
}

echo '<h1>' . ((count($tree)>0)?$tree[count($tree)-1]:basename(realpath('.'))) . '</h1>';

$dirs  = glob($currentDir."*",GLOB_ONLYDIR);
$files = glob($currentDir."*");

if (is_array($dirs))
foreach ($dirs as $dir) {
    $res = array_search($dir,$files);
    if ($res!==false) {
        unset($files[$res]);
        $files = array_values($files);
    }
}

//$nTree=$tree; array_pop($nTree);
if (count($tree)>0) {
    echo '<a href="index.php?path='.implodeUpTo("/",$tree,count($tree)-1).'/">[ .. ]</a><br>';
    echo "<br>";
}

if (is_array($dirs)) {
    foreach ($dirs as $dir) {
        if (strrpos($dir,"/")!==false)
            $dir=substr($dir,strrpos($dir,"/")+1);
        echo '<a href="index.php?path='.$currentDir.$dir.'/">[ '.$dir.' ]</a><br>';
        //echo '<tr><td><a href="'.$currentDir.$dir.'/">[ '.$dir.' ]</a></td></tr>';
    }
    echo "<br>";
}

if (is_array($files)) {
    echo '<table  cellpadding="5">';
    foreach ($files as $file) {
        if (strrpos($file,"/")!==false)
            $file=substr($file,strrpos($file,"/")+1);
        $file=substr($file,strrpos($file,"/"));
        if ($currentDir.$file!=='index.php') {
            $size=filesize($currentDir.$file);
            $unit="Bytes";

            if ($size>=1024) { $size /= 1024; $unit="KB"; }
            if ($size>=1024) { $size /= 1024; $unit="MB"; }
            if ($size>=1024) { $size /= 1024; $unit="GB"; }
            $size=round($size,0);

            //echo '<a href="'.$currentDir.$file.'" target="_blank">'.$file.'</a> - '."$size $unit <br>";
            echo '<tr><td><a href="'.$currentDir.$file.'" target="_blank">'.$file.'</a></td><td>'."$size $unit </td><td>" . date ("d.m.Y H:i:s.", filemtime($currentDir.$file)). "</td></tr>";
        }
    }
    echo "</table>";
}
?>

</body>

</html>
