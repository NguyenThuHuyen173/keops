<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
  
  $project_id = filter_input(INPUT_GET, "p_id");
  if (!isset($project_id)) {
    header("Location: /admin/#projects");
    die();
  }
  $project_dao = new project_dao();
  $project = $project_dao->getProjectById($project_id);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | New task for "<?= $project->name . " [" . $project->source_lang_object->langcode . "-" . $project->target_lang_object->langcode . "]" ?>"</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="page-header">
        <h1>New task for "<?= $project->name . " [" . $project->source_lang_object->langcode . "-" . $project->target_lang_object->langcode . "]" ?>" project</h1>
        <p>The task will be created for this project. To create a task for another project, create it using the "New task" button existing on that page.</p>
      </div>
      <form class="form-horizontal" action="/tasks/task_save.php" role="form" method="post" data-toggle="validator">
        <input type="hidden" name="project" value="<?= $project->id ?>">
        <?php
        $user_dao = new user_dao();
        $users = $user_dao->getUsers();
        ?>
        <div class="form-group">
          <label for="assigned_user" class="control-label col-sm-1">Evaluator</label>
          <div class="col-sm-4">
            <select class="form-control" name="assigned_user" id="assigned_user" tabindex="2">
              <?php foreach ($users as $user) { ?>
                <option value="<?= $user->id?>"><?= $user->name ?></option>
              <?php } ?>
            </select>
            <div id="helpCorpus" class="help-block with-errors">
              <?php if (count($users) == 0) { ?>
              No users available. Please <a href="/admin/index.php#users">click here</a> to invite new users.
              <?php } ?>
            </div>
          </div>
        </div>
        <?php
        $corpus_dao = new corpus_dao();
        $corpora_filters = array('active' => 'true', 'source_lang' => $project->source_lang, 'target_lang' => $project->target_lang);
        $corpora = $corpus_dao->getFilteredCorpora($corpora_filters);
        ?>
        <div class="form-group">
          <label for="corpus" class="control-label col-sm-1">Corpus</label>
          <div class="col-sm-4">
            <select class="form-control" name="corpus" id="corpus" tabindex="2">
              <?php foreach ($corpora as $corpus) { ?>
                <option value="<?= $corpus->id ?>"><?= $corpus->name ?></option>
              <?php } ?>
            </select>
            <div id="helpCorpus" class="help-block with-errors">
              <?php if (count($corpora) == 0) { ?>
              No corpora available for language pair <?= $project->source_lang_object->langcode . "-" . $project->target_lang_object->langcode ?>. Please <a href="/admin/index.php#corpora">click here</a> upload a corpus first.
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/admin/#projects" class="col-sm-offset-1 btn btn-danger" tabindex="6">Cancel</a>
            <button type="submit" class="col-sm-offset-1 btn btn-success" tabindex="5">Save</button>
          </div>
        </div>
      </form>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>