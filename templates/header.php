<!DOCTYPE html>

<html>

    <head>

        <link href="/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="/css/bootstrap-theme.min.css" rel="stylesheet"/>
        <link href="/css/styles.css" rel="stylesheet"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">


        <?php if (isset($title)): ?>
            <title>Merge: <?= strip_tags($title) ?></title>
        <?php else: ?>
            <title>Merge</title>
        <?php endif ?>

        <script src="/js/jquery-1.11.1.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/scripts.js"></script>
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

    </head>

    <body>

        <div class="container">

            <div id="top">
                <a href="/"><img alt="merge" width="240" src="/img/merge.png"/></a>
            </div>

            <div id="middle">
            
            <!-- navigation bar -->
            
            <ul class="nav nav-pills">
            <?php
                foreach ($links as $link)
                {
                    print("<li>");
                    print("<a href='{$link["url"]}'>{$link["title"]}</a>");
                    print("</li>");
                }
            ?>
            </ul>
            
            <?php
            // display title without using htmlspecialchars, ensuring title is pretty
            if (isset($title)) {
            ?>
            <h2><?= $title ?></h2>
            <?php
            }
            ?>
