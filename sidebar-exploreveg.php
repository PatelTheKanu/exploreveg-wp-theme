          <div id="right-nav">
	        <?php tha_sidebar_top(); ?>
            <div
              <?php if ( is_front_page() ) { echo 'id="front-page-actions" '; } ?>
              class="right-side-actions">

              <h2>Get Involved</h2>

              <div class="right-side-content">
                <?php
                     $form_id = get_option('exploreveg-announce-form-id');
                     if ($form_id) {
                         echo do_shortcode( '[contact-form-7 id="' . $form_id  . '" title="Announce Subscribe"]' );
                     }
                ?>

                <p>
                  <a href="/donate" class="btn btn-primary" title="Support our work">Donate</a>
                </p>

                <p>
                  <a href="/volunteer/" class="btn btn-primary" title="Make it all happen">Volunteer</a>
                </p>

                <p>
                  <a href="/programs/free-veg-starter-kit/" class="btn btn-primary" title="Order a free Vegetarian Starter Kit">Free Go Veg Kit</a>
                </p>

              </div>
            </div>

            <div id="announce-subscribe-modal" class="modal hide fade">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3></h3>
              </div>
              <div class="modal-body">
              </div>
              <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
              </div>
            </div>

            <h2>
              <a href="/events/" title="See all upcoming events">Upcoming Events</a>
            </h2>

            <div class="right-side-content">

              <?php
                 $event_format = <<<EOF
<div class="event">
  <h3 class="date">#_EVENTDATES</h3>
  <h3 class="event"><a href="#_EVENTURL">#_EVENTNAME</a></h3>
</div>
EOF;

                echo EM_Events::output(
                    array(
                        'scope'   => 'future',
                        'limit'   => 5,
                        'order'   => 'ASC',
                        'orderby' => 'event_start',
                        'format' => $event_format,
                        )
                    );
              ?>

            </div>
            <?php tha_sidebar_bottom(); ?>
          </div>
