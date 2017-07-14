<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\assets\AnalyticsChartAssets;
use app\assets\D3Assets;

D3Assets::register($this);
AnalyticsChartAssets::register($this);


$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="dealers-index">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            mainChart("<?= $report; ?>");
            $( function() {
                $( "#accordion" ).accordion({
                  collapsible: true
                });
                $( ".datepicker" ).datepicker();
                $( "#selectmenu" ).selectmenu();
            } );
        });
    </script>
    <script>

    </script>
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="col-md-3">
        <div id="accordion">
            <h3>Refine Metrics</h3>
            <div>
                <p>Date Range:</p>
                <input type="text" class="form-control datepicker"> - <input type="text" class="form-control datepicker">
                <br /><br />
                <p>Report Type:</p>
                <select id="selectmenu">
                    <option value="">All</option>
                    <option value="Content">Content</option>
                    <option value="Blogs">Blogs</option>
                    <option value="Microsites">Microsites</option>
                </select>
            </div>
            <h3>Other Dealers</h3>
            <div>
                <?php
                foreach ($dealers as $dealer) {
                    $pids = ArrayHelper::getColumn($dealer->gaProperties, 'id');
                    echo '<p>';
                    echo Html::a($dealer->name, ['/dashboard/aggregate',
                        'pids' => $pids], ['class' => 'dealerSelect']);
                    echo '</p>';
                }
                ?>
            </div>
        </div>
    </div>
    <div id="dash-right-col" class="col-md-9 panel panel-default">
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
        <svg class="mainChart" x="0" y="0"></svg>
    </div>
</div>