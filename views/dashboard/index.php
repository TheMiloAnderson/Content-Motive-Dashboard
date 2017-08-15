<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\assets\DashboardAssets;
use yii\widgets\Pjax;

DashboardAssets::register($this);

$this->title = 'Dashboard';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tertiary-nav">
    <div class="control-panel">
        <ul id="menu-content" class="menu-content out">
        <?php
        if (count($dealers) > 1) { ?>
            <li data-toggle="collapse" data-target="#dealers" class="collapsed">
                <a href="#"><span id="dealersSelectTitle">Dealers</span>
                    <div class="glyphicon glyphicon-menu-down" aria-hidden="true"></div></a>
            </li>
            <ul class="sub-menu collapse" id="dealers">
                <?php foreach ($dealers as $dealer) {
                    $pids = ArrayHelper::getColumn($dealer['properties'], 'id');
                    if ($pids) {
                        echo '<li><a class="dealerSelect" data-id="' . $dealer['id'] . '" ';
                        echo 'href="' . Url::to(['dashboard/aggregate', 'pids' => $pids]) . '">';
                        echo $dealer['name'] . '&nbsp;(' . count($pids) . ')' . '</a></li>';
                    }
                } ?>
            </ul>
        <?php } ?>
            <li id="websites-head" data-toggle="collapse" data-target="#websites" class="collapsed">
                <a href="#"><span id="websitesSelectTitle">Websites</span>
                    <div class="glyphicon glyphicon-menu-down" aria-hidden="true"></div></a>
            </li>
            <ul class="sub-menu collapse" id="websites">
                <li class="single-prop all-websites"><a class="prop-click" href="">All Websites</a></li>
                <?php foreach ($dealers as $dealer) {
                    foreach ($dealer['properties'] as $property) {
                        echo '<li class="single-prop" id="' . $property['id'] . '">';
                        echo '<a class="prop-click" href="' . $property['id'] . '">';
                        echo $property['url'] . '</a></li>';
                    }
                } ?>
            </ul>
        </ul>
    </div>
    <div class="control-panel">
        <div id="dateRow1">
            <input id="startDate" type="text" class="form-control date">
            <input id="endDate" type="text" class="form-control date">
        </div>
        <div id="dateRow2">
            <div id="slider-range"></div>
        </div>
        <div id="dateRow3" class="text-center">
            <button id="dateRangeBtn" type="button" class="btn btn-primary">Update</button>
            <button id="dateRangeResetBtn" type="button" class="btn btn-primary">Reset</button>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        dashboard.init("<?php echo Url::to(['dashboard/aggregate', 'pids' => ArrayHelper::getColumn($dealers[0]['properties'], 'id')]); ?>", [
            <?php foreach ($dealers as $dealer) {
                echo '{named: "' . $dealer['name'] . '", id: ' . $dealer['id'] . '},';
            } ?>
        ]);
    });

</script>
<!--<h1><?= Html::encode($this->title) ?></h1>-->
<div class="dash-panel" id="dash-panel-main">
    <h1 id="subhead">Content: 
        <span id="dealerSubhead"></span>
    </h1>
    <div class="dash-panel" id="dash-panel-secondary">
        <div class="metric-panel">
            <h4 class="metric-title">Pageviews</h3>
            <div id="pageviews-readout" class="readout pageviews">
                0
            </div>
        </div>
        <div class="metric-panel">
            <h4 class="metric-title">Visitors</h3>
            <div id="visitors-readout" class="readout visitors">
                0
            </div>
        </div>
        <div class="metric-panel">
            <h4 class="metric-title">Entrances</h3>
            <div id="entrances-readout" class="readout entrances">
                0
            </div>
        </div>
        <div class="metric-panel">
            <h4 class="metric-title">Avg Time</h3>
            <div id="duration-readout" class="readout duration">
                0:00
            </div>
        </div>
        <div class="metric-panel">
            <h4 class="metric-title">Bounce Rate</h3>
            <div id="bounce-readout" class="readout bounce">
                0%
            </div>
        </div>
    </div>
</div>
<div class="chartBox">
    <h3><span id="websiteSubhead"></span></h3>
    <svg class="mainChart"></svg>
</div>
<div id="tableBox">
    <?php Pjax::begin(['enablePushState' => false]); ?>
        <div id="p0">
        </div>
    <?php Pjax::end(); ?>
</div>