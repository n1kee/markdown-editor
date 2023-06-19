<?php

require_once 'app.php';

?>

<!-- Theme included stylesheets -->
<link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="//cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="src/css/jquery.dynatable.css">
<link rel="stylesheet" type="text/css" href="src/css/article-list.css">

<script type="text/javascript" src="src/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="src/js/jquery.dynatable.js"></script>

<!-- jQuery Modal -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

<link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>

<div class="agent-list-controls">
    <input placeholder="Filter by Title, Url, Content" type="text" name="search" class="search-query">

    <select id="select-tool" class="search-query" name="tool">
        <option value="">Select Tool to filter</option>
        <?php
        foreach ($toolList as $tool) {
        ?>
            <option value="<?= $tool ?>"><?= $tool ?></option>
        <?php
        }
        ?>
    </select>

    <select id="select-status" class="search-query" name="status">
        <option value="">Select Status to filter</option>
        <?php
        foreach ($statusList as $status) {
        ?>
            <option value="<?= $status ?>"><?= $status ?></option>
        <?php
        }
        ?>
    </select>

    <select id="select-author" class="search-query" name="author">
        <option value="">Select Author to filter</option>
        <?php
        foreach ($authorList as $author) {
        ?>
            <option value="<?= $author ?>"><?= $author ?></option>
        <?php
        }
        ?>
    </select>
</div>

<table class="agent-list">
    <thead>
        <tr>
        <?php
        foreach ($tableColumns as $idx => $column) {
        ?>
            <th class="<?= $column[ 'class' ] ?>">
                <?= $column['header'] ?? normalizeColumnName($column['name']) ?>
            </th>
        <?php
        }
        ?>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($articlesContent as $row) {
    ?>
        <tr>
            <?php
            foreach ($tableColumns as $column) {
            ?>
                <td class="<?= $column[ 'class' ] ?>">
                    <?= getCellContent($column, $row) ?>
                </td>
            <?php
            }
            ?>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>

<div id="editor-modal" class="modal">
    <div id="editor-container"></div>
    <a href="#" rel="modal:close">Close</a>
    <a id="editor-modal-save" href="#" rel="modal:close">Save</a>
</div>

<div id="error-modal" class="modal">
    <div>An unexpected error occured while saving !</div>
    <div>Please try again later !</div>
    <a href="#" rel="modal:close">Close</a>
</div>

<script type="text/javascript" src="src/js/article-list.js"></script>
