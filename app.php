<?php

define("ARTICLES_CONTENT_DIR", 'articles-content');
define("ARTICLES_META_DIR", 'articles-meta');

$postRequest = json_decode(file_get_contents('php://input'), true);

if ($postRequest) {
    writeArticleContent($postRequest['article-url'], $postRequest['article-content']);
    die();
}

if (isset($_GET['article-url'])) {
    $articleContent = getArticle($_GET['article-url'], ARTICLES_CONTENT_DIR);
    die($articleContent);
}

$articlesList = scandir(ARTICLES_META_DIR);
$articlesList = array_slice($articlesList, 2);
$articlesListIndexes = array_keys($articlesList);

$toolList = [];
$statusList = [];
$authorList = [];

$articlesContent = [];

foreach ($articlesList as $fileIdx => $fileName) {
    $articleMeta = getArticle($fileName);
    $articleContent = getArticle($fileName, ARTICLES_CONTENT_DIR);
    preg_match_all('/(.+?):\s*"?(((?<=")[^"]*)|(.+))/', $articleMeta, $articleMetaParsed);
    list(, $articleMetaKeys, $articleMetaValues) = $articleMetaParsed;
    $articleMetaParsed = array_combine(
        $articleMetaKeys,
        $articleMetaValues
    );
    $articleData = array_map(
        function($value) { return json_decode($value) ?? $value; },
        $articleMetaParsed
    );

    if ($articleData[ 'author' ]) {
        $authorList[] = $articleData[ 'author' ];  
    }

    if ($articleData[ 'status' ]) {
        $statusList[] = $articleData[ 'status' ];  
    }
    
    if ($articleData[ 'tool' ]) {
        $toolList[] = $articleData[ 'tool' ];
    }

    $articleData['idx'] = ++$fileIdx;
    $articleData['url'] = $fileName;
    $articleData["downloadUrl"] = "/articles/{$fileName}";
    $articleData["contentForSearch"] = htmlentities($articleContent);
    $articleData["modalId"] = "edit-row-" .  $articleData["id"];
    $articleData["editorId"] = "editor-container-" .  $articleData["id"];

    $articlesContent[ $fileName ] = $articleData;
}

$toolList = array_unique($toolList);
$statusList = array_unique($statusList);
$authorList = array_unique($authorList);

function getArticle($articleUrl, $articleFilePath = ARTICLES_META_DIR) {
    $articlePath = getArticlePath($articleUrl, $articleFilePath);
    return file_get_contents($articlePath);
}

function writeArticleContent($articleUrl, $articleContent) {
    writeArticle($articleUrl, $articleContent, ARTICLES_CONTENT_DIR);
}

function writeArticle($articleUrl, $articleText, $articleFilePath = ARTICLES_META_DIR) {
    $articlePath = getArticlePath($articleUrl, $articleFilePath);
    return file_put_contents($articlePath, $articleText);
}

function getArticlePath($articleUrl, $articleFilePath = ARTICLES_META_DIR) {
    $articleUrl = basename($articleUrl);
    return implode(DIRECTORY_SEPARATOR, [ $articleFilePath, $articleUrl ]);
}

function normalizeColumnName($columnName) {
    $columnName = preg_replace("/[^A-Za-z]+/", " ", $columnName);
    $columnName = trim($columnName);
    return strtoupper($columnName);
}

function parseCellContent($cellContent, $row) {
    $placeholders = array_keys($row);
    $placeholders = array_map(function($ph) { return "%{$ph}%"; }, $placeholders);
    $values = array_values($row);
    return str_replace($placeholders, $values, $cellContent);
}

function getCellContent($column, $row) {
    if ($column['content']) {
        return parseCellContent($column['content'], $row);
    }
    return $row[ $column['name'] ];
}

$tableColumns = [
    [ "name" => "idx", "header" => "#" ],
    [ "name" => "url", "content" => "<a href='%downloadUrl%'>%url%</a>" ],
    [ "name" => "title" ],
    [ "content" => "<div class='btn article-action-btn article-edit-btn' data-article-url='%url%'>EDIT</div>" ],
    [ "name" => "status" ],
    [ "name" => "author" ],
    [ "name" => "category" ],
    [ "name" => "tool" ],
    [ "name" => "views" ],
    [ "name" => "published_on" ],
    [ "name" => "modified_on" ],
    [ "content" => "<div class='btn article-action-btn article-upublish-btn' data-article-url='%url%'>UNPUBLISH</div>" ],
    [ "name" => "content", "content" => "%contentForSearch%", "class" => "hidden" ],
];
