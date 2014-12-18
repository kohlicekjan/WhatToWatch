<section class="panel">
    <article>       
        <?php
        echo $this->navAccount;
        ?>
    </article>
</section>
<section class="concent">
    <article>
        <h2><?php echo Session::get('user')['name']; ?></h2>
        <div>E-mail: <?php echo $this->email; ?></div>
        <div>Poslední přihlášení: <?php echo Session::get('user')['lastlogin']; ?></div>
    </article>
</section>