<div id="<?php echo 'ebook-'.get_the_ID(); ?>" class="pgafu-medium-4 pgafu-columns filtr-item">
    <div class="pgafu-post-grid">
        <div class="pgafu-post-grid-content">
            <div class="pgafu-post-image-bg">
                <a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
                    <?php the_post_thumbnail( 'full' ); ?>                    
                </a>
            </div>
            <h2 class="pgafu-post-title">
                <a href="<?php the_permalink();?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
            </h2>
            <div class="pgafu-post-categories">
                <?php 
                    $cat = 'ebook-category';
                    $postcats = get_the_terms($post->ID, $cat);
                    if($postcats) {
                        foreach ( $postcats as $term ) {
                            $term_link = get_term_link( $term );
                            echo '<a href="' . esc_url( $term_link ) . '">'.$term->name.'</a>';
                        }
                    }
                ?>
            </div>
            <div class="pgafu-post-content">
                <div class="pgafu-post-short-content"><?php the_excerpt(); ?></div>
                <?php $amazon = get_post_meta( get_the_ID(), '_ebook_amazon', true ); ?>
                <a href="<?php echo $amazon;?>" title="<?php the_title(); ?>" class="readmorebtn" target="_blank">BUY AT AMAZON</a>
            </div>
            <div class="pgafu-post-date">
                <span class="pgafu-time"> 
                    PUBLISHED ON: <?php echo get_the_date(); ?> 
                </span>
            </div>
            <input type="hidden" id="paged" class="" value="1">
        </div>
    </div>
</div>