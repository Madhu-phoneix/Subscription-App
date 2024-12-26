<?php
  include("header.php");
?>
<div class="Polaris-Layout">
<?php
include("navigation.php");
$all_videos_array = array(
  "How to add subscription widget block" => "https://your-domain.com/application/assets/videos/subscription-widget.mp4",
  "How to create a subscription plan" => "https://www.youtube.com/embed/M6HTaEfSofg",
  "Customer subscriptions on merchant dashboard" => "https://www.youtube.com/embed/7Z_cfHlVg-g",
  "Customers portal settings" => "https://www.youtube.com/embed/da7X18qdxjk"
);
?>
<div class="Polaris-Layout__Section sd-dashboard-page">
  <div>
    <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
      <div class="Polaris-Page-Header__Row">
        <div class="Polaris-Page-Header__TitleWrapper">
          <div>
            <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
              <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Video Tutorials</h1>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="Polaris-Page__Content">
        <div id="list_subscription_wrapper" class="">
            <div class="cardboxes">
                <div class="Polaris-Layout subscription-videos">
                    <?php foreach($all_videos_array as $key => $value){ ?>
                        <div class="subscription-video-card Polaris-Layout__Section Polaris-Layout__Section--oneHalf">
                            <div class="Polaris-Card">
                                <div class="sd-upper-wrapper">
                                    <div class="Polaris-Card__Header">
                                        <div class="Polaris-Stack Polaris-Stack--alignmentCenter">
                                            <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                            <h2 class="Polaris-Heading subscription_heading"><span class="list_planname"><?php echo $key; ?></span></h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="mini_922484886" class="subscription_mini_inner_wrapper">
                                        <div class="Polaris-Card__Section inner-box-cont bot-bx">
                                            <iframe id="create_plans" width="100%" src="<?php echo $value; ?>" frameborder="0" allowfullscreen=""></iframe>
                                        </div>
                                    </div>
                                </div><!-- header wrapper -->
                                <!-- content wrapper -->
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<?php
   include("footer.php");
?>