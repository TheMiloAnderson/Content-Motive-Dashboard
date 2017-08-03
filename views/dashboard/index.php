<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\assets\AnalyticsChartAssets;
use yii\widgets\Pjax;

AnalyticsChartAssets::register($this);

$this->title = 'Dashboard';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tertiary-nav">
    <div class="control-panel">
        <p>Date Range:</p>
        <input id="startDate" type="text" class="form-control date"> - <input id="endDate" type="text" class="form-control date">
        <br /><br />
        <div id="slider-range"></div>
        <div class="text-center">
            <button id="dateRangeBtn" type="button" class="btn btn-primary">Update Data</button>
        </div>
    </div>
    <div class="control-panel">
        <ul id="menu-content" class="menu-content out">
        <?php
        if (count($dealers) > 1) { ?>
            <li data-toggle="collapse" data-target="#dealers" class="collapsed">
                <a href="#">Dealers<span class="arrow"></span></a>
            </li>
            <ul class="sub-menu collapse in" id="dealers">
                <?php foreach ($dealers as $dealer) {
                    $pids = ArrayHelper::getColumn($dealer['properties'], 'id');
                    if ($pids) {
                        echo '<li><a class="dealerSelect" href="' . Url::to(['dashboard/aggregate', 'pids' => $pids]) . '">';
                        echo $dealer['name'] . '&nbsp;(' . count($pids) . ')' . '</a></li>';
                    }
                } ?>
            </ul>
        <?php } ?>
            <li id="websites-head" data-toggle="collapse" data-target="#websites" class="collapsed">
                <a href="#">Websites<span class="arrow"></span></a>
            </li>
            <ul class="sub-menu collapse in" id="websites">
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
</div>
<div class="dealers-index">
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
    <h1 id="subhead">Content: 
        <span id="dealerSubhead"></span>
    </h1>
    <div class="col-md-2">
<div class="row">
            <div class="col-md-5ths">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Pageviews</h3>
                    </div>
                    <div id="pageviews-readout" class="panel-body readout">
                        0
                    </div>
                </div>
            </div>
            <div class="col-md-5ths">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Visitors</h3>
                    </div>
                    <div id="visitors-readout" class="panel-body readout">
                        0
                    </div>
                </div>
            </div>
            <div class="col-md-5ths">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Entrances</h3>
                    </div>
                    <div id="entrances-readout" class="panel-body readout">
                        0
                    </div>
                </div>
            </div>
            <div class="col-md-5ths">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Avg. Duration</h3>
                    </div>
                    <div id="duration-readout" class="panel-body readout">
                        0:00
                    </div>
                </div>
            </div>
            <div class="col-md-5ths">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Bounce Rate</h3>
                    </div>
                    <div id="bounce-readout" class="panel-body readout">
                        0%
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="dash-right-col" class="col-md-10 panel panel-default">
        <div class="row">
            <div class="col-md-12">
                <h3><span id="websiteSubhead"></span></h3>
            </div>
        </div>
        
        <svg class="mainChart" x="0" y="0"></svg>
        <?php Pjax::begin(); ?>
            <div id="p0">
            </div>
        <?php Pjax::end(); ?>
    </div>
</div>