<?php
$data_extenso =  $this->customFields[$this->ID]['data_extenso'];
?>
<li class="span3">
    <?php if( has_post_thumbnail()) : ?>
        <a href="<?= $this->permalink ?>" title="<?= $this->title; ?>" class="wpmg_image_featured">
            <?php the_post_thumbnail( 'medium' ); ?>
        </a>
    <?php endif; ?>
    <div class="masonry-item-inner caption">
        <h6><?php echo $data_extenso ?></h6>
        <h3><a href="<?= $this->permalink ?>" title="<?= $this->title ?>"><?= $this->title  ?></a></h3>
        <p>Vale do Jequitinhonha</p>
        <p>Celeiro inventivo e artístico muito peculiar no estado, o Vale do Jequitinhonha é berço de belas e criativas manifestações: trabalhos em Couro, Bordados, Tecelagem, Desenho, Música, Esculturas em Madeira, Cestaria, Pintura e a Cerâmica. </p>
    </div>
</li>
