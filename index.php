<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>WhatToWatch</title>
        <link rel="stylesheet" href="public/css/default.css" type="text/css" media="all">
    </head>
    <body>
        <header>
            <nav>            
                <a href="" class="menuBlue activeBlue">WhatToWatch</a>
                <a href="" class="menuGreen">Novinky</a>
                <a href="" class="menuYellow">Žebříčky</a>
                <a href="" class="menuRed">Tvůrci</a>
                <a href="" class="menuBlack">Přihlásit</a>
            </nav>
        </header>
        <section class="panel">
             
            <article>
                <form action="index.php" method="post">
                    <input type="hidden" name="token" value="ce947047d1ed26edd6efed8f31e43bd7">
                    <div class="formField">
                        <label>Uživatelské jméno</label>
                        <input type="text" name="name" value="">
                        <span>Zadejte své uživatelské jméno.</span>
                    </div>
                    <div class="formField">
                        <label>Heslo</label>
                        <input type="password" name="password" value="">
                        <span>Zadejte své heslo.</span>
                    </div>
                    <div class="formButton">
                        <input type="submit" name="send" value="Přihlásit">
                        <a href="?page=recovery">Zapomenuté heslo</a>
                    </div>
                </form>
            </article>
        </section>
        <section class="concent">
             
            <article>
                <h1>Nadpis</h1>
                <p>
                    Text je v lingvistice spojitý jazykový útvar, který se uplatňuje v komunikaci jako komplexní jednotka sdělování. Označuje uskupení slov, které dohromady tvoří nějakou výpověď. Pojem se obvykle používá pro psané výpovědi delšího rozsahu (odstavec), lze jej ovšem užít i pro mluvený jazykový projev nebo i krátké výpovědi. Knihy jsou označovány jako umělecké texty, novinové články jako publicistické texty, atd.

Text má následující vlastnosti:[1]

komplexnost: skládá se z menších částí (vět),
spojitost, koherenci: jeho jednotky (věty) jsou navzájem propojeny, je mezi nimi zřejmá souvislost,
funkčnost, informativnost: je tvořen se záměrem, kterým chce autor působit na adresáta,
organizovanost: jeho jednotky jsou uspořádány podle vzájemných souvislostí,
zapojení do kontextu a komunikační situace: text je postaven tak, aby mu adresáti porozuměli.
Je jasné, že některé z těchto vlastností textu mohou být záměrně nebo nedopatřením porušeny.
                </p>
            </article>
        </section>
        <footer>copyright © 2014 Jan Kohlíček</footer>
    </body>
</html>
