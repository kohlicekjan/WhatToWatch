<section class="panel">
    <article>
        <?php echo $this->form; ?>
        <?php echo $this->formViews; ?>
    </article>
</section>
<?php if (!empty($this->csfd_id)) { ?>
    <section class="concent">
        <article>
            <div class="creation">
                <div class="creationPoster">
                    <img src="<?php echo $this->poster_url; ?>" alt="Plakát">
                </div>
                <div class="creationRating">
                    <div class="ratingCSFD">
                        ČSFD.cz<br>
                        <span class="rating"><?php echo $this->rating; ?>%</span>
                    </div>
                    <div class="ratingIMDB">
                        IMDB.com<br>
                        <span class="rating"></span>
                    </div>
                </div>
                <div class="creationInfo">
                    <div class="creation_title">
                        <h2><?php echo $this->name_cs; ?></h2>
                        <h3 id="name_en"><?php echo $this->name_en; ?></h3>
                        <span id="csfd_id"><?php echo $this->csfd_id; ?></span>    
                    </div>
                    <div>
                        <div><?php echo $this->genre; ?></div>
                        <div ><span id="countries"></span><span id="year"><?php echo $this->release; ?></span>, <?php echo $this->type; ?> - <?php echo $this->runtime; ?> min</div>
                    </div>    
                    <div>
                        <div id="directors"></div>
                        <div id="actors"></div>
                    </div>
                </div>
            </div>
        </article>
        <article>
            <h2>Popis</h2>
            <p><?php echo $this->plot; ?></p>
        </article>
    </section>
    <script src="/WhatToWatch/public/js/load_csfd.js"></script>
<?php } else { ?>
    <section class="concent">
        <article>
            <h2>Žádné dílo nesplnilo nesplnilo podmínky.</h2>
            <h3></h3>
        </article>
    </section>
<?php } ?>
