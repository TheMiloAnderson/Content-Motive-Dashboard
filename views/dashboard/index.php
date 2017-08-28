<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\assets\DashboardAssets;
use yii\widgets\Pjax;

DashboardAssets::register($this);

$this->title = 'Dashboard';

?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        dashboard.init("<?php echo Url::to(['dashboard/aggregate', 'pids' => ArrayHelper::getColumn($dealers[0]['properties'], 'id')]); ?>", [
            <?php foreach ($dealers as $dealer) {
                echo '{named: "' . $dealer['name'] . '", id: ' . $dealer['id'] . '},';
            } ?>
        ]);
    });
</script>

<div class="chartBox">
    <div id="controlsBox">
        <div class="controls">
            <div class="control-panel select">
                <ul id="menu-content" class="menu-content out">
                <?php
                if (count($dealers) > 1) { ?>
                    <li data-toggle="collapse" data-target="#dealers" class="collapsed">
                        <div id="dealersSelectTitle">Dealers</div>
                    </li>
                    <ul class="sub-menu collapse" id="dealers">
                        <?php foreach ($dealers as $dealer) {
                            $pids = ArrayHelper::getColumn($dealer['properties'], 'id');
                            if ($pids) {
                                echo '<li class="dealerSelect" data-id="' . $dealer['id'] . '" ';
                                echo 'href="' . Url::to(['dashboard/aggregate', 'pids' => $pids]) . '">';
                                echo $dealer['name'] . '&nbsp;(' . count($pids) . ')' . '</li>';
                            }
                        } ?>
                    </ul>
                <?php } ?>
                    <li id="websites-head" data-toggle="collapse" data-target="#websites" class="collapsed">
                        <div id="websitesSelectTitle">Websites</div>
                    </li>
                    <ul class="sub-menu collapse" id="websites">
                        <li class="propertyFilter all-websites"><a class="prop-click" href="">All Websites</a></li>
                        <?php foreach ($dealers as $dealer) {
                            foreach ($dealer['properties'] as $property) {
                                echo '<li class="propertyFilter" id="' . $property['id'] . '">';
                                //echo '<a class="prop-click" href="' . $property['id'] . '">';
                                echo $property['url'] . '</li>';
                            }
                        } ?>
                    </ul>
                </ul>
            </div>
            <div class="control-panel date">
                <div id="dateRow1">
                    <div id="dateCol1">
                        <input id="startDate" type="text" class="form-control date">
                        <input id="endDate" type="text" class="form-control date">
                    </div>
                    <div id="dateCol2">
                        <button id="dateRangeBtn" type="button" class="btn btn-primary">Update</button>
                        <button id="dateRangeResetBtn" type="button" class="btn btn-primary">Reset</button>
                    </div>
                </div>
                <div id="dateRow2">
                    <div id="slider-range"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="chart">
        <h3 id="subhead">Content: 
            <span id="dealerSubhead"></span>
        </h3>
        <h4><span id="websiteSubhead"></span></h4>
        <svg class="mainChart"></svg>
    </div>
</div>
<div class="tableBox">
    <div class="readoutBox">
        <div class="metric-panel">
            <h4 class="metric-title">Entrances</h4>
            <div id="entrances-readout" class="readout entrances">
                0
            </div>
        </div>
        <div class="metric-panel">
            <h4 class="metric-title">Visitors</h4>
            <div id="visitors-readout" class="readout visitors">
                0
            </div>
        </div>
        <div class="metric-panel">
            <h4 class="metric-title">Pageviews</h4>
            <div id="pageviews-readout" class="readout pageviews">
                0
            </div>
        </div>
        <div class="metric-panel">
            <h4 class="metric-title">Avg Time</h4>
            <div id="duration-readout" class="readout duration">
                0:00
            </div>
        </div>
        <div class="metric-panel">
            <h4 class="metric-title">Bounce</h4>
            <div id="bounce-readout" class="readout bounce">
                0%
            </div>
        </div>
    </div>
    <?php Pjax::begin(['enablePushState' => false, 'timeout' => 10000]); ?>
        <div id="p0">
        </div>
    <?php Pjax::end(); ?>
</div>