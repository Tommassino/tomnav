<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
    <script type="text/javascript">
      $(function(){
        $("#tabs").tabs();
        $(".tooltip-trigger").each(function(){
          $(this).tooltip({
            content: function() {return $("#tooltip-"+$(this).attr("id")).html();},
          });
        });
        $("#overlay").hide();$("#overlay-content").hide();
        $("#addpos").button().click(function(){
          $("#overlay").show();
          $("#overlay-content").show();
          $(".overlay-pane").hide();
          $("#posform").show();
          $("#posform textarea").focus();
        });
        $(".closeoverlay").click(function(){
          $("#overlay").hide();
          $("#overlay-content").hide();
        });
        $(".posinfo").each(function(){
          $(this).click(function(){
            $("#overlay").show();
            $("#overlay-content").show();
            $(".overlay-pane").hide();
            $("#posdetails").show();
            $("#posdetails-content").html($("#tooltip-"+$(this).attr("id")).html());
          });
        });
      });
    //CCPEVE.requestTrust("http://sormumble.no-ip.info/eve/*");
    </script>
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <link rel="stylesheet" href="css/style.css" />
    <title>TomNav</title>
  </head>
  <body>
    <div id="wrapper">
<?
if(isset($_GET["debug"]) || isset($_COOKIE["debug"])){
  $_SERVER['HTTP_EVE_SOLARSYSTEMNAME']="J123458";
  $_SERVER['HTTP_EVE_CHARNAME']="Tommassino Preldent";
  $_SERVER['HTTP_EVE_CHARID']=91004506;
  setcookie("debug","true");
}
if(!isset($_SERVER['HTTP_EVE_SOLARSYSTEMNAME']) && !isset($_GET['locus']))
  die();
if(isset($_SERVER['HTTP_EVE_SOLARSYSTEMNAME']))
  $query = $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'];
if(isset($_GET['locus']))
  $query=$_GET['locus'];

$locus = Locus::getLocus($query);
$statics = $locus->getStatics();
$intel = $locus->getIntel(1);
$poses = $locus->getPosIntel();
$effect = $locus->getField('effect');
$ch=null;
if(isset($_SERVER['HTTP_EVE_CHARID']) && isset($_SERVER['HTTP_EVE_CHARNAME']))
  $ch = CharacterInfo::getCharacter($_SERVER['HTTP_EVE_CHARID'],$_SERVER['HTTP_EVE_CHARNAME']);
$sigs = $locus->getSignatures();
?>
    <div id="sinfo-wrapper">
    <div id="sinfo"><div id="sinfo-in">
      <div class="header">System Info</div>
      <div class="class<?= $locus->getField('class') ?>">
        System: <?= $locus->getField('name') ?><br />
        Class: <?= $locus->getField('class') ?><br />
        <div <?= $effect>0?"class=\"tooltip-trigger\"":"" ?> id="effect<?= $locus->getField('effect') ?>" title="">Effect: <?= $locus->getEffect() ?><br /></div>
      </div>
      <div id="tooltip-effect<?= $locus->getField('effect') ?>" class="hide">
<?
$details = $effectDetails[$effect];
foreach($details as $name=>$type){
?>
        <div class="<?= $type>0?"bonus":"penalty" ?>"><?= $name ?>: <?= $effectValues[abs($type)][$locus->getField('class')-1]?> <?= $type>0?"bonus":"penalty" ?></div>
<?
}
?>
      </div>
<?
foreach($statics as $static){
?>
        <div class="statics class<?= $static->getField('type') ?> tooltip-trigger" title="" id="static_<?= $static->getField('id')?>">
          Static: <?= $static->getField('name') ?><br />
          Class: <?= $static->getType() ?><br />
          Signature: <?= $static->getField('signaturesize') ?><br />
        </div>
        <div id="tooltip-static_<?= $static->getField('id')?>" class="hide">
          Lifetime: <?= $static->getField('lifetime'); ?> h<br />
          Maximum jump mass: <?= $static->getField('jumpmass'); ?> kg<br />
          Total mass: <?= $static->getField('totalmass'); ?> kg<br />
        </div>
<?
}
?>
      </div>
    </div>
    <div id="skills">
    <div id="tabs">
      <ul>
        <li><a href="#tabs-1">Ship Kills</a></li>
        <li><a href="#tabs-2">Npc Kills</a></li>
        <li><a href="#tabs-3">Pod Kills</a></li>
      </ul>
      <div id="tabs-1"><img src="includes/graph.php?id=<?= $locus->getField('id') ?>&amp;type=0" alt="Ship Kills"/></div>
      <div id="tabs-2"><img src="includes/graph.php?id=<?= $locus->getField('id') ?>&amp;type=1" alt="Npc Kills"/></div>
      <div id="tabs-3"><img src="includes/graph.php?id=<?= $locus->getField('id') ?>&amp;type=2" alt="Pod Kills"/></div>
    </div>
    </div>
    <div id="pos"><div id="pos-in">
<?
foreach($poses as $pos){
  $corp = $pos->getCorporation();
?>
      <div class="tooltip-trigger posinfo" id="pos<?= $pos->getField('id')?>" title=""><?= $pos->getField('location') ?></div>
      <div id="tooltip-pos<?= $pos->getField('id') ?>" class="hide">
        <div class="header"><?= $pos->getField('posType') ?></div>
<?
  if($corp!=null && $corp->getField('ccpId')!=null ){
?>
        <div><a href="#" onclick="CCPEVE.showInfo(2, <?= $corp->getField('ccpId') ?>)"><?= $corp->getField('corporationName') ?></a></div>
<?
  }
?>
        <div class="modules"><?= $pos->getField('modules') ?></div>
        <div class="signature">
          <?= $pos->getAuthor()->getField('characterName') ?>
          <?= $pos->getField('updated') ?>
        </div>
      </div>
<?
}
if($ch){
?>
      <input id="addpos" type="submit" value="Add POS" />
<?
}
?>
    </div></div></div>
    <div id="message-pane">
     <div class="header">System Intel</div>
<?
foreach($intel as $in){
?>
      <div class="message">
        <div class="data"><?= $in->getField('data') ?></div>
        <div class="signature">by <?= $in->getAuthor()->getField('characterName') ?> on <?= $in->getField('updated') ?></div>
      </div>
<?
}

if($ch){
?>
      <div>
        <form action="?aaddintel" method="post">
          <fieldset>
          <input type="hidden" value="?locus=<?= $query ?>" name="redirect" />
          <input type="hidden" value="<?= $locus->getField('id')?>" name="locusid"/>
          <input type="hidden" value="<?= $ch->getField('id') ?>" name="characterid" />
          <input type="hidden" value="1" name="type" />
          <div class="header">Add message</div>
          <div class="intelform">
            <textarea name="data" cols="0" rows="4"></textarea>
            <input type="submit" />
          </div>
          </fieldset>
        </form>
      </div>
<?
}
?>
      <div class="header">Signature Intel</div>
      <table>
        <tr><th>ID</th><th>Type</th><th>Name</th><th>Strength</th></tr>
<?
foreach($sigs as $sig){
  if($sig->isOld()){
    $sig->delete();
    continue;
  }
?>
        <tr><td><?= $sig->getField("sigid") ?></td><td><?= $sig->getType() ?></td><td><?= $sig->getField("signame") ?></td><td><?= $sig->getField("sigstrength") ?></td></tr>
<?
}
?>
      </table>
<?
if($ch){
?>
      <div>
        <form action="?aaddsigs" method="post">
          <fieldset>
            <input type="hidden" value="?locus=<?= $query ?>" name="redirect" />
            <input type="hidden" value="<?= $locus->getField('id')?>" name="locusid"/>
            <div class="header">Add signatures</div>
            <div class="intelform">
              <textarea name="data" cols="0" rows="4">
JDD-250				10%	
INM-888			Unstable Wormhole       100%	
NNM-628				20%	
GNM-392				30%	
KNM-384				40%	
IMS-560				10%	
LMS-811				20%	
MMS-228				30%	
KMS-394				40%	
YCV-488				50%	
HMS-143				40%	
MLR-844				30%	
</textarea>
              <input type="submit" />
            </div>
          </fieldset>
        </form>
      </div>
<?
}
?>
    </div>
    </div>
    <div id="overlay">
      <div id="overlay-content">
        <div id="posdetails" class="hide overlay-pane">
          <a class="closeoverlay"><img src="temp/apple-close.png" alt="close"/></a>
          <div id="posdetails-content"></div>
        </div>
        <div id="posform" class="hide overlay-pane">
          <a class="closeoverlay"><img src="temp/apple-close.png" alt="close"/></a>
          <div class="header">Add POS</div>
          <form action="?aaddpos" method="post">
            <fieldset>
            <input type="hidden" value="?locus=<?= $query ?>" name="redirect" />
            <input type="hidden" value="<?= $locus->getField('id')?>" name="locusid"/>
            <input type="hidden" value="<?= $ch?$ch->getField('id'):"" ?>" name="characterid" />
            <div id="instructions">How to add a pos: <ol><li>be on grid with the pos</li><li>deactivate your active overview settings in dscan</li><li>hit dscan and copy/paste the dscan output in the textarea below</li></ol></div>
            <textarea name="dscan" rows="0" cols="4"></textarea>
            <div>Corporation:<input type="text" name="corporationname" /></div>
            <input type="submit" value="Submit"/>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
