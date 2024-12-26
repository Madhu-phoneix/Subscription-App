<?php
include("header.php");
?>
<div class="Polaris-Layout">
<?php
include("navigation.php");
?>
<div class="Polaris-Layout__Section sd-dashboard-page">

<div class="Polaris-Page contact_form">
	<div class="Polaris-Page__Content">
	<div style="--top-bar-background:#00848e; --top-bar-background-lighter:#1d9ba4; --top-bar-color:#f9fafb; --p-frame-offset:0px;">
	<div class="Polaris-Page-Header__TitleAndRollup">
		<div class="Polaris-Page-Header__Title">
			<h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Contact Us</h1>
		</div>
	</div>
  <div class="Polaris-FormLayout">
  <div class="Polaris-FormLayout__Items">
		<div class="Polaris-FormLayout__Item">
		  <div p-color-scheme="light">
  <div class="">
    <div class="Polaris-Select"><select id="sd_query_type" class="Polaris-Select__Input" aria-invalid="false">
        <option value="General">General Query</option>
        <option value="Suggestion">Suggestion</option>
      </select>
      <div class="Polaris-Select__Content" aria-hidden="true"><span class="Polaris-Select__SelectedOption">General Query</span><span class="Polaris-Select__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
              <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
            </svg></span></span></div>
      <div class="Polaris-Select__Backdrop"></div>
    </div>
  </div>
  <div id="PolarisPortalsContainer"></div>
</div>
		</div>
	</div>
	<div class="Polaris-FormLayout__Items">
		<div class="Polaris-FormLayout__Item">
		  <div class="">
			<div class="Polaris-Connected">
			  <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
				<div class="Polaris-TextField"><input class="Polaris-TextField__Input" type="text" maxlength="30" aria-labelledby="PolarisTextField2Label" aria-invalid="false" aria-multiline="false" placeholder="Enter Name here" id="sd_customerName" name="name" autocomplete="off">
				  <div class="Polaris-TextField__Backdrop"></div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	</div>
	<div class="Polaris-FormLayout__Items">
		<div class="Polaris-FormLayout__Item">
		  <div class="">
			<div class="Polaris-Connected">
			  <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
				<div class="Polaris-TextField"><input class="Polaris-TextField__Input" type="text" maxlength="50" aria-labelledby="PolarisTextField2Label" aria-invalid="false" aria-multiline="false" id="sd_customerShop" placeholder="Enter Store URL (abcd.myshopify.com)" name="website" value="<?php echo $_GET['shop']; ?>" autocomplete="off">
				  <div class="Polaris-TextField__Backdrop"></div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	</div>
	<div class="Polaris-FormLayout__Items">
		<div class="Polaris-FormLayout__Item">
		  <div class="">
			<div class="Polaris-Connected">
			  <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
				<div class="Polaris-TextField"><input class="Polaris-TextField__Input" type="text" maxlength="50" aria-labelledby="PolarisTextField2Label" aria-invalid="false" aria-multiline="false" id="sd_customerEmail" placeholder="Enter email here" name="email" autocomplete="off">
				  <div class="Polaris-TextField__Backdrop"></div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	</div>
	<div class="Polaris-FormLayout__Items">
		<div class="Polaris-FormLayout__Item">
		  <div class="">
			<div class="Polaris-Connected">
			<div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
			<div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline"><textarea class="Polaris-TextField__Input" aria-labelledby="PolarisTextField4Label" aria-invalid="false" aria-multiline="true" style="height: 108px;" rows="10" cols="10" id="sd_customerMsg" name="message" placeholder="Describe issue here" spellcheck="false"></textarea>
			  <div class="Polaris-TextField__Backdrop"></div>
			</div>
		  </div>
		</div>
	</div>
	</div>
    </div>
	<button type="button" class="contactus_button Polaris-Button Polaris-Button--primary" id="sd_contactUs"><span class="Polaris-Button__Content">
	<span class="Polaris-Button__Text">Send Mail</span></span></button>
	<div class="error"></div>
	  <div class="response"></div>
  </div>
</div>
</div>
</div>
</div>
<?php include("footer.php"); ?>