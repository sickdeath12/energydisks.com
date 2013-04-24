<div id="edit_menu_item_<?php echo $item->id() ?>" class="<?php echo $item->editFormClasses() ?>" title="Edit menu item">

	<h1 class="pp-iframe-title pp-iframe-title-menus">
		<span class="icon"></span>
		Customize ProPhoto <b>Menu</b> Item
	</h1>

	<form id="edit-menu-item-form" action="" method="post" accept-charset="utf-8">

		<div id="jquery-tabs">

			<ul class="nav">
				<li id="details"><a href="#link-details">Link Details</a></li>
				<li id="display"><a href="#link-display">Link Display</a></li>
				<li><a href="#link-advanced">Advanced</a></li>
			</ul>

			<?php do_action( 'pp_menu_item_edit_notices' ); ?>

			<p class="gmail-warn"><span></span></p>

			<div id="link-details">

				<p class="select-type-explanation">
					<em>First</em>, please select a link-type for this new item:
				</p>

				<label id="link-type">Link type:</label>

				<div id="link-type-blurbs-wrap">

					<p id="blurb-container">
						A "container" is meant simply to hold sub-links. So, if you're creating some sort of dropdown, start by
						creating a container link, then create and drag other new links into this container item.
					</p>

					<p id="blurb-manual">
						Use this link type if you're linking to an external website, or anytime you just want to directly enter
						any URL (web address), like <b>"http://www.somesite.com/page/"</b>.
					</p>

					<p id="blurb-internal">
						Easily create links to existing parts of your blogsite, like WordPress "Pages", archives, categories,
						recent posts, and ProPhoto galleries.
					</p>

					<p id="blurb-special">
						A grab bag of special-purpose menu items like Twitter, email link, search forms, subscribe by email,
						and slide-down hidden content.
					</p>

				</div>


				<?php

				/* Link type */
				echo NrHtml::radio( 'type', array(
					'Container for other menu items' => 'container',
					'Directly entered URL link'      => 'manual',
					'Internal blog links'            => 'internal',
					'Special link types'             => 'special',
				), $item->type );

				?>


				<div id="type-dependent-internal" class="dependent-option-group">

					<?php

					/* Internal link type */
					$dropdown = $item->isInWidgetMenu() ? '' : 'dropdown';
					$internalLinkTypes = array(
						"Specific WordPress Page"         => 'page',
						"WordPress Pages $dropdown&nbsp;" => 'pages',
						"Specific category"               => 'category',
						"Categories $dropdown"            => 'categories',
						"Monthly archives $dropdown"      => 'archives',
						"Specific ProPhoto gallery"       => 'gallery',
						"RSS Feed"                        => 'rss',
						"Recent posts $dropdown"          => 'recent_posts',
						"Blog home page link"             => 'home',
					);
					if ( $item->isInMobileMenu() ) {
						unset( $internalLinkTypes['RSS Feed'] );
					}
					echo NrHtml::label( 'Internal link type: ', 'internalType' );
					echo NrHtml::select( 'internalType', $internalLinkTypes, $item->internalType );

					/* page ID */
					echo NrHtml::select( 'pageID', ppMenuAdmin::pages(), $item->pageID );

					/* page load method */
					if ( !$item->isInWidgetMenu() && !$item->isInMobileMenu() ) {
						echo NrHtml::radio( 'pageLoadMethod', array(
							'load page normally' => 'standard',
							'slide-down page content on same page' => 'ajax_slide_down',
						), $item->pageLoadMethod ? $item->pageLoadMethod : 'standard' );
					}


					/* exclude specific pages */
					echo '<div id="exclude-pages-wrap" class="sc">';
						echo NrHtml::label( 'Check any pages you <b>do not</b> want included in the dropdown:', 'exclude' );
						foreach ( ppMenuAdmin::pages() as $pageName => $pageID ) {
							if ( $pageID ) {
								echo '<span>';
									echo NrHtml::labledCheckbox(
										$pageName,
										'exclude_pageID_' . $pageID,
										in_array( $pageID, explode( ',', $item->excludedPageIDs ) ),
										$pageID
									);
								echo '</span>';
							}
						}
					echo '</div>';

					/* category name */
					echo NrHtml::select( 'categoryName', ppMenuAdmin::categories(), $item->categoryName );

				 	/* archive dropdown nesting threshold */
					if ( !$item->isInMobileMenu() && !$item->isInWidgetMenu() ) {
						echo '<div id="archives-nest-wrap">';
							echo NrHtml::textInput( 'archivesNestThreshold', $item->archivesNestThreshold, 2 );
							echo NrHtml::label( ' Months displayed before switching to year-based sub-dropdowns', 'archivesNestThreshold' );
						echo '</div>';
					}

					/* archive dropdown nesting threshold */
					echo '<div id="recent-posts-num-wrap">';
						echo NrHtml::textInput( 'numRecentPosts', $item->numRecentPosts, 2 );
						echo NrHtml::label( ' Number of recent posts displayed', 'numRecentPosts' );
					echo '</div>';

					/* gallery display method */
					echo NrHtml::hiddenInput( 'galleryID', $item->galleryID );
					$galleryDisplayTypes = array(
						'select gallery display method...'  => '',
						'Slideshow in popup window'         => 'popup_slideshow',
						'Fullscreen slideshow popup window' => 'fullscreen_popup_slideshow',
						'Slideshow in page'                 => 'slideshow_in_page',
						'Slideshow loaded into slidedown'   => 'slideshow_in_slidedown',
						'Lightbox loaded into slidedown'    => 'lightbox_in_slidedown',
						'Lightbox in page'                  => 'lightbox_in_page',
					);
					if ( $item->isInMobileMenu() ) {
						unset( $galleryDisplayTypes['Slideshow in popup window'] );
						unset( $galleryDisplayTypes['Fullscreen slideshow popup window'] );
						unset( $galleryDisplayTypes['Slideshow loaded into slidedown'] );
						unset( $galleryDisplayTypes['Lightbox loaded into slidedown'] );
					}
					echo NrHtml::select( 'galleryDisplay', $galleryDisplayTypes, $item->galleryDisplay )

					?>

					<div id="load-galleries" class="sc">
						<span id="loading-throbber">
							<img src="http://prophoto.s3.amazonaws.com/img/ajaxLoadingSpinner.gif" class="throbber" />
							Loading gallery previews...
						</span>
					</div>

				</div>

				<div id="type-dependent-special" class="dependent-option-group">

					<?php

					/* Special link type */
					$specialTypes = array(
						'Email link'                     => 'email',
						'Twitter feed'                   => 'twitter',
						'Inline search form&nbsp;'       => 'inline_search',
						'Dropdown search form'           => 'dropdown_search',
						'Subscribe by email form&nbsp;'  => 'subscribe_by_email',
						'Show hidden contact form&nbsp;' => 'show_contact_form'
					);
					if ( ppBio::mightBeMinimized() && !$item->isInMobileMenu() ) {
						$specialTypes['Show hidden bio area'] = 'show_bio';
					}
					if ( $item->isInWidgetMenu() || $item->isInMobileMenu() ) {
						unset( $specialTypes['Dropdown search form'] );
						if ( $item->isInMobileMenu() ) {
							unset( $specialTypes['Twitter feed'] );
							unset( $specialTypes['Subscribe by email form&nbsp;'] );
							unset( $specialTypes['Show hidden contact form&nbsp;'] );
						}
					} else {
						$specialTypes['Slide down custom text/HTML&nbsp;'] = 'show_custom_html';
					}
					if ( $item->isInMobileMenu() ) {
						$specialTypes['Call telephone # link'] = 'call_telephone';
					}
					echo NrHtml::label( 'Special link type: ', 'specialType' );
					echo NrHtml::select( 'specialType', $specialTypes, $item->specialType );

					/* Email link address */
					echo NrHtml::labledTextInput( 'Email address:', 'email', $item->email, 55 );

					/* Twitter ID */
					echo NrHtml::labledTextInput( 'Twitter ID:', 'twitterID', $item->twitterID, 35 );

					/* Number of Tweets shown */
					echo '<div id="num-tweets-wrap">';
					echo NrHtml::textInput( 'numTweets', $item->numTweets ? $item->numTweets : ppMenuItem_Twitter::DEFAULT_NUM_TWEETS, 2 );
					echo NrHtml::label( ' number tweets shown', 'numTweets' );
					echo '</div>';

					/* Search form submit button text */
					echo NrHtml::labledTextInput(
						'Search submit button text:',
						'searchBtnText',
						$item->searchBtnText ? $item->searchBtnText : ppMenuItem_Search::DEFAULT_BTN_TXT,
						45
					);

					/* Subscribe by email options */
					echo NrHtml::labledTextInput(
						'Text that appears inside email form', 'subscribeByEmailPrefill',
						$item->subscribeByEmailPrefill ? $item->subscribeByEmailPrefill : ppMenuItem_SubscribeByEmail::DEFAULT_PREFILL,
						45
					);
					echo NrHtml::labledTextInput(
						'Subscribe submit button text:',
						'subscribeByEmailBtnText',
						$item->subscribeByEmailBtnText ? $item->subscribeByEmailBtnText : ppMenuItem_SubscribeByEmail::DEFAULT_BTN_TXT,
						45
					);

					/* telephone number */
					echo NrHtml::labledTextInput(
						'Telephone number:',
						'telephoneNumber',
						$item->telephoneNumber ? $item->telephoneNumber : ''
					);

					/* custom HTML */
					echo '<div id="custom-html-wrap">';
						echo NrHtml::label( 'Custom text/html:', 'customHTML' );
						echo NrHtml::textarea( 'customHTML', $item->customHTML, 6 );
					echo '</div>';

					?>

				</div>

				<?php

				/* Link text */
				echo NrHtml::labledTextInput( 'Link text:', 'text', esc_attr( $item->text ), 45 );

				/* URL */
				echo NrHtml::labledTextInput( 'URL:', 'url', $item->url, 75 );

				?>

				<div id="type-dependent-container" class="dependent-option-group">
					<p>To add <b>sub-items</b> into this container, close this popup and drag menu items <b>into</b> the container item.</p>
					<img src="http://prophoto.s3.amazonaws.com/img/nest-menu-item-screenshot.jpg" />
				</div>


				<?php


				/* Target */
				echo '<div id="target-wrap">';
					echo NrHtml::label( 'When clicked, link opens in ', 'target' );
					echo NrHtml::select( 'target', array(
						'same window/tab&nbsp;' => '',
						'new window/tab' => '_blank',
					), $item->target, 'id=select-target' );
				echo '</div>';

				?>



			</div>

			<div id="link-display">

				<?php

				/* Anchor */
				echo NrHtml::span( 'Link display type:' );
				echo NrHtml::radio( 'anchor', array(
					'text' => 'text',
					'image' => 'img',
					'text &amp; icon&nbsp;' => 'text_and_icon',
				), $item->anchor );

				/* Image */
				ppUploadBox::renderImg( $item->id(), 'Menu image' );

				/* Icon alignment */
				echo NrHtml::label( 'Icon alignment:', 'iconAlign' );
				echo NrHtml::radio( 'iconAlign', array(
					'to the left of text' => 'left',
					'to the right of text' => 'right',
				), $item->iconAlign ? $item->iconAlign : 'left' );

				if ( !$item->isInWidgetMenu() ) {
					echo NrHtml::labledCheckbox( 'constrain icon height to text height', 'iconConstrained', $item->iconConstrained() );
				}

				/* Icon */
				ppUploadBox::renderImg( $item->id() . '_icon', 'Menu icon' );

				?>

			</div>


			<div id="link-advanced">

				<?php

				$home = pp::site()->hasStaticFrontPage ? 'posts' : 'home';

				echo '<div id="hide-menu-item-on">';
					echo NrHtml::label( 'Click to <b>hide</b> menu item on various page types:', 'hide-menu-item-on' );
					echo NrHtml::labledCheckbox( 'hide on individual "Post" pages', 'hideOnSingle',     $item->hideOnSingle );
					echo NrHtml::labledCheckbox( 'hide on individual "Page" pages', 'hideOnPage',       $item->hideOnPage );
					if ( pp::site()->hasStaticFrontPage ) {
						echo NrHtml::labledCheckbox( 'hide on static front page',   'hideOnFront_page', $item->hideOnFront_page );
					}
					echo NrHtml::labledCheckbox( "hide on blog $home page",         'hideOnHome',       $item->hideOnHome );
					echo NrHtml::labledCheckbox( 'hide on category listing pages',  'hideOnCategory',   $item->hideOnCategory );
					echo NrHtml::labledCheckbox( 'hide on monthly archive pages',   'hideOnArchive',    $item->hideOnArchive );
				echo '</div>';

				?>

				<?php echo NrHtml::labledTextInput( 'Title attribute:', 'titleAttr', $item->titleAttr, 55 ); ?>

				<?php echo NrHtml::labledTextInput( 'Custom CSS classes:', 'customClasses', $item->customClasses, 43 ); ?>
				<span class="text-input-note">separate multiple classnames with spaces</span>

				<div id="rel-nofollow-wrap">
					<?php echo NrHtml::labledCheckbox( 'add <code>rel="nofollow"</code> to <code>&lt;a&gt;</code> tag', 'relNofollow', $item->relNofollow ); ?>
				</div>

			</div>

		</div> <!-- #jquery-tabs -->

		<?php echo NrHtml::hiddenInput( 'menu_item_id', $item->ID ); ?>

		<input type="submit" value="Save changes" class="button">
		<input type="submit" value="Close popup" class="button" id="done-editing">

	</form>

</div>