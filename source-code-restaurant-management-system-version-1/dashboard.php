                <?php

                include('rms.php');
                session_start();
				$object = new rms();
				if(!$object->is_login())
				{
				    header("location:".$object->base_url."");
				}

                include('header.php');

                ?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

                    <!-- Content Row -->
                    <div class="row">
                        <?php
                        if($object->is_master_user())
                        {
                        ?>
                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4 daily_sales">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Today Sales</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $object->Get_total_today_sales(); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4 monthly_sales">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Monthly Sales</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $object->Get_monthly_sales(); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4 outstanding_sales">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Outstanding Sales
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $object->Get_outstanding_sales(); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4 hourly_sales">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Hourly Sales</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $object->Get_hourly_sales();?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4 cash_sales">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                CASH</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $object->Get_cash_sales();?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4 card_sales">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                CARD</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $object->Get_card_sales();?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4 upi_sales">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                UPI</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $object->Get_upi_sales();?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4 item_wise_sales">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Item Wise Summary</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php
                    }
                    ?>
                        <div class="col-md-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="m-0 font-weight-bold text-primary">Live Table Status</h6>
                                        </div>
                                        <div class="col" align="right">
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="table_status"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>
<script>

$(document).ready(function(){

    reset_table_status();

    setInterval(function(){
        reset_table_status();
    }, 5000);

    function reset_table_status()
    {
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{action:'dashboard_reset'},
            success:function(data){
                $('#table_status').html(data);
            }
        });
    }

});

$(document).on('click', '.cash_sales', function(){
        window.location.href="<?php echo $object->base_url; ?>cash_report.php?date="
});

$(document).on('click', '.card_sales', function(){
        window.location.href="<?php echo $object->base_url; ?>card_report.php?date="
});

$(document).on('click', '.upi_sales', function(){
        window.location.href="<?php echo $object->base_url; ?>upi_report.php?date="
});

$(document).on('click', '.outstanding_sales', function(){
        window.location.href="<?php echo $object->base_url; ?>outstanding_report.php?date="
});

$(document).on('click', '.daily_sales', function(){
        window.location.href="<?php echo $object->base_url; ?>daily_report.php"
});

$(document).on('click', '.monthly_sales', function(){
        window.location.href="<?php echo $object->base_url; ?>monthly_report.php"
});

$(document).on('click', '.hourly_sales', function(){
        window.location.href="<?php echo $object->base_url; ?>hourly_report.php"
});

$(document).on('click', '.item_wise_sales', function(){
        window.location.href="<?php echo $object->base_url; ?>item_wise_report.php"
});
</script>