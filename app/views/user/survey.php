<section class="panel">
    <article>
         <?php
        echo $this->navAccount;
        ?>
    </article>
</section>
<section class="concent">
    <article>
        <h2><?php echo $this->title ?></h2>
       <?php echo '<a href="'.URL.'user/add/" class="normalButton">Nový</a>'; ?>
        <?php 
        echo $this->tableUser;
        ?>
    </article>
</section>