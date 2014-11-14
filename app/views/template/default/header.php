<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $this->pageTitle; ?></title>

        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
        <link rel="shortcut icon" href="">
        <link rel="stylesheet" href="/WhatToWatch/public/css/default.css" type="text/css" media="all">
    </head>
    <body>
        <h1 style="display: none;">
            <a href="" title="WhatToWatch">WhatToWatch</a>
        </h1>
        <header>
            <nav> 
                <ul>
                    <?php
                    $menu = ['' => ['Blue', 'WhatToWatch'],
                        'news/' => ['Green', 'Novinky'],
                        'rankings/' => ['Yellow', 'Žebříčky'],
                        'creators/' => ['Red', 'Tvůrci'],
                        'acount/loginForm/' => ['Black', 'Přihlásit']];

                    foreach ($menu as $key => $value) {
                        echo '<li><a href="' . URL . $key . '" class="menu' . $value[0] . ($_SERVER['REQUEST_URI'] == URL . $key ? ' active' . $value[0] : '') . '">' . $value[1] . '</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </header>
        <?php
        
        //error, spravne, info
        //foreach ($this->message as $key => $value) {
        //    echo '<div class=""></div>';
        //}
        ?>

