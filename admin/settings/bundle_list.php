<?php
  include("../header.php");
?>
  <div class="Polaris-Layout">
<?php
    include("../navigation.php");
?>

<div class="Polaris-Layout__Section  sd-bundle_list-page">
    <div>
        <div class="Polaris-Card__Header sd_contractHeader">
            <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                    <h2 class="Polaris-Heading">Create Bundle</h2>
                </div>
                <div class="Polaris-Stack__Item">
                    <div class="Polaris-ButtonGroup">
                        <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                            <button class="Polaris-Button Polaris-Button--primary sd_button sd_create_bundle navigate_element" type="button" data-query-string="" data-redirect-link="/subscription/admin/settings/create_bundle.php?shop=<?php echo $mainobj->store; ?>" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                            <span class="Polaris-Button__Content">
                            <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Create Bundle</span></span>
                            </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="Polaris-Page__Content">
            <div>
                <div class="">
                    <div class="Polaris-DataTable sd_common_datatable">
                        <div class="Polaris-DataTable__ScrollContainer">
                            <div id="subscriptionTable_wrapper" class="dataTables_wrapper no-footer">
                                <div class="sd_subscription_table">
                                    <table class="Polaris-DataTable__Table dataTable no-footer" id="subscriptionTable" role="grid" aria-describedby="subscriptionTable_info">
                                        <thead>
                                            <tr role="row">
                                                <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--firstColumn Polaris-DataTable__Cell--header sorting_disabled" scope="col" aria-sort="none" rowspan="1" colspan="1" style="width: 242.734px;">Name</th>
                                                <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--firstColumn Polaris-DataTable__Cell--header sorting_disabled" scope="col" aria-sort="none" rowspan="1" colspan="1" style="width: 65.7031px;">Status</th>
                                                <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric sorting_disabled" scope="col" aria-sort="none" rowspan="1" colspan="1" style="width: 31.2656px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="Polaris-DataTable__TableRow Polaris-DataTable--hoverable odd" role="row">
                                                <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric">
                                                    <div class="sd_customerData">
                                                        <span class="user-name"></span>
                                                        <div class="sd_customerDetails"><strong>neha bhagat</strong><br><span>nehaa.shinedezign@gmail.com</span></div>
                                                    </div>
                                                </td>
                                                <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric">
                                                    <a class="Polaris-Navigation__Item navigate_contract_detail" onmouseover="show_title(this)" onmouseout="hide_title(this)" data-query-string="6320849149" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">#6320849149</a>
                                                    <div class="Polaris-PositionedOverlay display-hide-label">
                                                        <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                                            <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">View</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><a class="Polaris-Navigation__Item" href="https://mini-cart-development.myshopify.com/admin/orders/5490056265981" target="_blank" data-polaris-unstyled="true">#1923</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="PolarisPortalsContainer"></div>
            </div>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>
