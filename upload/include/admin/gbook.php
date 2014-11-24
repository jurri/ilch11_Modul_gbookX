<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

##
###
####
##### A k t i o n e n


if ( !empty ($_GET['del']) ) {
  $id = escape($_GET['del'], 'integer');
  db_query("DELETE FROM `prefix_gbook` WHERE id = ".$id." LIMIT 1");
  db_query("DELETE FROM prefix_koms WHERE uid = ".$id." AND cat = 'GBOOK'");

}

if ( !empty ($_GET['status']) ) {
  $statusid = escape($_GET['status'], 'integer');
  db_query("UPDATE `prefix_gbook` SET `status` = IF(`status`>0,0,1) WHERE `id` = $statusid LIMIT 1");

}

if (isset($_POST['sub'])) {
  $name = escape($_POST['name'], 'string');
  $mail = escape($_POST['mail'], 'string');
  $page = escape($_POST['page'], 'string');
  $text = escape($_POST['text'], 'string');
  $status = escape($_POST['status'], 'integer');   
  if (empty($_POST['gid'])) {
    db_query("INSERT INTO prefix_gbook (name, mail, page, txt, time, status) VALUES ('".$name."','".$mail."','".$page."','".$text."', '".time()."','".$status."')");
  } else {
    $gid  = escape($_POST['gid'], 'integer');
    db_query("UPDATE prefix_gbook SET name = '".$name."', mail = '".$mail."', page = '".$page."', txt = '".$text."' , status = $status WHERE id = ".$gid);
  }
}
$ar = array(1 => 'Freigegeben', 0 => 'Gesperrt');
$r = array ('name'=>'','mail'=>'','page'=>'','text'=>'','id'=>'','status'=>arlistee(1,$ar));
if (isset($_GET['edit'])) {
  $id = escape($_GET['edit'], 'integer');
  $r = db_fetch_assoc(db_query("SELECT id, name, mail, page, txt as text, status FROM prefix_gbook WHERE id = ".$id)); 
  $r['status'] = arlistee($r['status'],$ar); 
}

$tpl = new tpl ('gbook', 1);
$tpl->set_ar_out($r,0);

$class = '';
$erg = db_query('SELECT name, mail, txt, id, status FROM `prefix_gbook` ORDER BY time DESC');
while ($r = db_fetch_assoc($erg) ) {
  $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
  $staus = '<a href="admin.php?gbook&status='.$r['id'].'"><img src="include/images/icons/' . ($r['status'] == 1 ? 'jep' : 'nop') . '.gif" title="' . $ar[$r['status']] . '" alt="Status" border="0" /></a>';  
  $text  = substr(preg_replace("/\015\012|\015|\012/", " ", htmlentities(strip_tags(stripslashes($r['txt'])), ILCH_ENTITIES_FLAGS, ILCH_CHARSET)),0,75);
  echo '<tr class="'.$class.'">';
  echo '<td>'.$staus.'&nbsp;<a href="admin.php?gbook=0&edit='.$r['id'].'"><img src="include/images/icons/edit.gif" /></a>&nbsp;<a href="javascript:delcheck('.$r['id'].')"><img src="include/images/icons/del.gif"></a></td>';
  echo '<td><b><a href="mailto:'.$r['mail'].'">'.$r['name'].'</a></b>&nbsp;<span class="smalfont">';
  echo $text.'</span></td>';
  echo '</tr>';
}

$tpl->out(1);

$design->footer();
?>