<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $this->pageTitle; ?></title>

        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
        <link rel="stylesheet" href="/WhatToWatch/public/css/default.css" type="text/css" media="all">
    </head>
    <body>       
        <header>
            <h1 style="display: none;">
                <a href="" title="WhatToWatch">WhatToWatch</a>
            </h1>
            <?php
            $nav = new Navigation();
            $nav->addItem('', 'WhatToWatch');
            $nav->addItem('news/', 'Novinky');
            $nav->addItem('recommend/', 'Doporučení');
            if (empty(Session::get('user'))) {
                $nav->addItem('account/login/', 'Přihlásit');
            } else {
                
                $subNav= new Navigation();
                $subNav->addItem('account/logout/', 'Odhlásit');
                
                $nav->addItem('account/', Session::get('user')['name']);
                $nav->setSubItem($subNav->generateHTML(true));
            }

            echo $nav->generateHTML();
            
            
            ?>

        </header>
        <?php
        foreach ($this->messages as $message) {
            echo '<div class="' . $message['state'] . '">' . $message['text'] . '</div>';
        }
        ?>

