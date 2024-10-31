<?php
/**
 * The Template for displaying all single Business.
 *
 * @package indianic
 * @subpackage 1.0.1
 * @since indianic
 */
get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>


			<header class="entry-header">
			<?php the_post_thumbnail(); ?>
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
			<?php if ( comments_open() ) : ?>
                <div class="comments-link">
	                <?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'twentytwelve' ) . '</span>', __( '1 Reply', 'twentytwelve' ), __( '% Replies', 'twentytwelve' ) ); ?>
                </div><!-- .comments-link -->
            <?php endif; // comments_open() ?>
            </header>
            
            <div class="entry-content">
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?>
                <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
            </div><!-- .entry-content -->
			<?php             
            global $wpdb; 
          
			$Portiondata = get_post_meta($post->ID,'advportions');
			$Portiondata = unserialize($Portiondata[0]);
			//print_r($Portiondata);	
			//echo "<br>";
			$PortionPrices = get_post_meta($post->ID,'advprices');
			$PortionPrices = unserialize($PortionPrices[0]);
			//print_r($PortionPrices);
			//echo "<br>";		
			$post_item_image = get_post_meta($post->ID,'post_item_image',ARRAY_A);
	
			?>        
              
       <style>
		#basicTable tr td
		{
		/*padding:5px;
		margin:5px;*/
		border:1px solid #ccc !important;
		padding:5px;
		
		
		}
		#feedback { font-size: 1.4em; }
		#basicTable tr td:hover { background: #FECA40; }
		.selected { background: #F39814; color: white; }
		.reserve { background: #1C94C4; color: white;} 
		
		
		</style>
        <p> <b><?php echo __("Iteam Size", "nicadvtext"); ?> : </b>
        <?php		 
		echo $Portiondata['heightcount'];
		echo '&nbsp;X&nbsp;';
		echo $Portiondata['widthcount'];
		?>
        </p>
         <table cellpadding="10" cellspacing="10" border="1" style="margin:15px 0px; clear:both; float:none;">
            <tr>
                <td valign="middle">&nbsp;<?php echo __("Available", "nicadvtext"); ?>&nbsp;  <span style="background:#F8F8F8; height:15px; width:15px; float:left; border:1px solid #ccc;">&nbsp;</span></td>
                <td valign="middle">&nbsp;<?php echo __("Reserved", "nicadvtext"); ?>&nbsp;  <span style="background:#1C94C4; height:15px; width:15px; float:left; margin-left:5px; border:1px solid #ccc;">&nbsp;</span></td>
            </tr>        
        </table>
       <span id="box" style="height: 150px; width: 150px; z-index:9; float:right; ">
            <table id="basicTable" cellspacing="5" cellpadding="5" width="150" height="150" >
                <?php
				$args = array(
				'numberposts'     => -1,
				'post_type'       => 'sponser',
				'post_parent'     => $post->ID
				);
				$Advert_array = get_posts( $args );
				
				foreach($Advert_array as $Advertisement)
				{
					$AdvertId = $Advertisement->ID;
					if(isset($AdvertId) && $AdvertId != '')
					{
						$MyMetas = get_post_meta($AdvertId);
						$partition_Arr[] = $MyMetas['sponser_partition'][0];
					}
				}
				
                if(!empty($Portiondata))
                {
                    $cnt = 1;
                    $inc = 0;
                    for ($i = 0; $i < $Portiondata['rowcount']; $i++) 
                    {
                        echo '<tr calss="class1 class2 class3">';
                        for ($j = 0; $j < $Portiondata['columncount']; $j++) 
                        {
							$style = '';
							$myId = 'id="create-user"';
							$reserve = '';
							if(!empty($partition_Arr) && in_array($cnt, $partition_Arr))
							{
								$style = 'reserve';
								$myId = ' ';								
								$reserve = 'onclick="alert('."'Partition is reserved'".');"';
							}
                            echo '<td align="center" '.$myId.' '.$reserve.' class="ui-state-default '.$style.'" lang="'.$cnt.'" style="cursor:pointer;">$'.$PortionPrices[$inc].'</td>';
                            $cnt++;
                            $inc++;
                        }
                        echo '</tr>';					
                    }
                }
                ?>
            </table>
        </span>
        <span style="float:left"><img src="<?php echo $post_item_image; ?>" /></span>
       
        <span style="clear:both">&nbsp;</span> 
       
        <br />
    
        
        
            <footer class="entry-meta">
                <?php twentytwelve_entry_meta(); ?>
                <?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
                <?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
                    <div class="author-info">
                        <div class="author-avatar">
                            <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentytwelve_author_bio_avatar_size', 68 ) ); ?>
                        </div><!-- .author-avatar -->
                        <div class="author-description">
                            <h2><?php printf( __( 'About %s', 'twentytwelve' ), get_the_author() ); ?></h2>
                            <p><?php the_author_meta( 'description' ); ?></p>
                            <div class="author-link">
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
                                    <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentytwelve' ), get_the_author() ); ?>
                                </a>
                            </div><!-- .author-link	-->
                        </div><!-- .author-description -->
                    </div><!-- .author-info -->
                <?php endif; ?>
            </footer><!-- .entry-meta -->
            
            <div id="dialog-form" title="Reserve Your Area">
            <p class="validateTips"><?php echo __("All form fields are required.", "nicadvtext"); ?></p>
            <br />
            <?php global $post; ?>
            <form name="FrmSponser" id="FrmSponser" action="" method="post" enctype="multipart/form-data">
            <fieldset>
            <label for="name"><?php echo __("Name", "nicadvtext"); ?> :</label>
            <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" /><br />
            <label for="email"><?php echo __("Email", "nicadvtext"); ?> :</label>
            <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" /><br />
            <label for="contact"><?php echo __("Contact No", "nicadvtext"); ?> :</label>
            <input type="text" name="contact" id="contact" class="text ui-widget-content ui-corner-all" /><br />
            <label for="description"><?php echo __("Description", "nicadvtext"); ?> :</label>
            <textarea name="description" id="description" class="textarea ui-widget-content ui-corner-all"></textarea><br />
            <input type="hidden" name="site_name" id="site_name" value="<?php echo site_url(); ?>" />
            <input type="hidden" name="postId" id="postId" value="<?php echo $post->ID; ?>" />  
            
            </fieldset>
            </form>
            <form id="imageform" method="post" enctype="multipart/form-data" action='<?php echo site_url(); ?>/wp-admin/admin-ajax.php'>
            <label for="banner"><?php echo __("Banner", "nicadvtext"); ?> :</label>
            <input type="file" name="banner" id="banner" class="file ui-widget-content ui-corner-all" />
            <input type="hidden" name="upload" id="upload" value="upload" />
            <input type="hidden" name="action" id="action" value="uploadImg"  />
            <input type="hidden" name="sponser" id="sponser" value=""  />
            <span class="close"></span>
            </form>
            <div id='preview'>
            </div>
            </div>

				<nav class="nav-single">
					<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
					<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentytwelve' ) . '</span> %title' ); ?></span>
					<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentytwelve' ) . '</span>' ); ?></span>
				</nav><!-- .nav-single -->

				<?php comments_template( '', true ); ?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>