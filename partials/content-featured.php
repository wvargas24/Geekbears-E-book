<article class="elementor-post elementor-grid-item <?php echo 'post-'.get_the_ID().' '.get_post_type().' type-'.get_post_type().' status-'.get_post_status(); ?> has-post-thumbnail hentry">
      <a class="elementor-post__thumbnail__link" href="<?php the_permalink();?>" title="<?php the_title(); ?>">
            <div class="elementor-post__thumbnail elementor-fit-height">
                  <?php the_post_thumbnail( 'full' ); ?>
            </div>
      </a>
      <div class="elementor-post__text">            
            <div class="elementor-post__meta-data">
                  <span class="elementor-post-date"><?php echo get_the_date(); ?></span>
            </div>
            <h3 class="elementor-post__title">
                  <a href="<?php the_permalink();?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
            </h3>
            <div class="elementor-post__excerpt">
                  <?php 
                        $excerpt = get_the_excerpt();
                        $excerpt = substr( $excerpt , 0, 100); 
                        echo '<p>'.$excerpt.'...</p>';
                  ?>
                  <?php $amazon = get_post_meta( get_the_ID(), '_ebook_amazon', true ); ?>
                  <a href="<?php echo $amazon;?>" title="<?php the_title(); ?>" class="readmorebtn" target="_blank">BUY AT AMAZON</a>
            </div>
      </div>
</article> 