<?php
/*
 * Page for the preview of the first sentences of a corpus
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");
//  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
//  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
  
  $corpus_id = filter_input(INPUT_GET, "id");
  if (!isset($corpus_id)) {
    header("Location: /admin/index.php#corpora");
    die();
  }
  $corpus_dao = new corpus_dao();
  $corpus = $corpus_dao->getCorpusById($corpus_id);
  $language_dao = new language_dao();
  $languages = $language_dao->getLanguages();
  $corpus_sl = "";
  $corpus_tl = "";
          
  $preview = $corpus_dao->getSentencesFromCorpus($corpus_id, 20);
  
  foreach ($languages as $lang){
    if ($lang->id == $corpus->source_lang){
      $corpus_sl = $lang->langname;
    }
    if ($lang->id == $corpus->target_lang){
      $corpus_tl = $lang->langname;
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Corpus preview </title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#corpora">Corpora</a></li>
        <li class="active"><?= $corpus->name?></a></li>
      </ul>
      <div class="page-header row vertical-align">
        <div class="col-xs-6 col-md-6">
          <h1><?= $corpus->name?></h1>
        </div>
        <div class="col-xs-6 col-md-6 text-right">
          <a href="/corpora/corpus_edit.php?id=<?php echo $corpus->id;?>" type="submit" title="Edit corpus" class="btn btn-link">
            <span class="glyphicon glyphicon-edit"></span>
            <span class="col-xs-12">Edit</span>
          </a> 
          <a href="/corpora/corpus_remove.php?id=<?php echo $corpus->id;?>" type="submit" title="Remove corpus" class="btn btn-link">
            <span class="text-danger">
              <span class="glyphicon glyphicon-trash"></span>
              <span class="col-xs-12">Remove</span>
            </span>
          </a>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <p><strong>ID:</strong> <?=$corpus->id ?></p>
        </div> 
        <div class="col-md-3">
          <p><strong>Name:</strong> <?=$corpus->name ?></p>
        </div>
        <div class="col-md-3">
          <p><strong>Source language: </strong> <?= $corpus_sl?></p>
        </div>
        <div class="col-md-3">
          <p><strong>Target language: </strong> <?= $corpus_tl?></p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
           <p><strong>Lines: </strong> <?= $corpus->lines?></p>
        </div>
        <div class="col-md-3">
           <p><strong>Creation date: </strong> <?= getFormattedDate($corpus->creation_date) ?></p>
        </div>
        <div class="col-md-3">
           <p><strong>Active?: </strong>  <input disabled type="checkbox" name="active"<?= $corpus->active ? " checked" : "" ?>></p>
        </div>
      </div>
      <div class="title-container">
        <h3>Corpus preview</h3>
      </div>
      
        <?php
        foreach ($preview as $line){
          ?>
          <div class="row">
            <div class="col-md-12 sentence-source"><?php echo $line->source_text;?></div>
            <div class="col-md-12 sentence-target"><?php echo $line->target_text;?></div>
          </div>
        <hr>
        <?php
        }
        ?>
      </div>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>