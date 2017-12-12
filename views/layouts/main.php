<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\models\UsersWithDealers;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    
    NavBar::begin([
        'brandLabel' => Html::img('@web/images/contentmotive-80x53.png', ['alt'=>Yii::$app->name])
            . '<div id="logoTxt"><span class="con">content</span><br /><span class="MOT">MOTIVE</span></div>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Content', 'url' => ['/dashboard/content']],
            ['label' => 'Admin', 'items' => [
                ['label' => 'Users', 'url' => ['/users/index']],
                ['label' => 'Dealers', 'url' => ['/dealers/index']],
                ],
            ],
            ['label' => 'Logout (milo)', 'linkOptions' => ['class' => 'logout']]
//            (UsersWithDealers::userHasContentType('Content')) ? (
//            ['label' => 'Content', 'url' => ['/dashboard/content']]) : (''),
//            (UsersWithDealers::userHasContentType('Blogs')) ? (
//            ['label' => 'Blogs', 'url' => ['/dashboard/blogs']]) : (''),
//            (UsersWithDealers::userHasContentType('Reviews')) ? (
//            ['label' => 'Reviews', 'url' => ['/dashboard/reviews']]) : (''),
//            (UsersWithDealers::userHasContentType('Microsites')) ? (
//            ['label' => 'Microsites', 'url' => ['/dashboard/microsites']]) : (''),
//            ((!Yii::$app->user->isGuest) && Yii::$app->user->identity->isAdmin()) ? (
//            ['label' => 'Admin', 'items' => [
//                ['label' => 'Users', 'url' => ['/users/index']],
//                ['label' => 'Dealers', 'url' => ['/dealers/index']],
//            ],
//            ]) : (''),
//            Yii::$app->user->isGuest ? (
//                ['label' => 'Login', 'url' => ['/site/login']]
//            ) : (
//                '<li>'
//                . Html::beginForm(['/site/logout'], 'post')
//                . Html::submitButton(
//                    'Logout (' . Yii::$app->user->identity->username . ')',
//                    ['class' => 'btn btn-link logout']
//                )
//                . Html::endForm()
//                . '</li>'
//                ['label' => 'Logout (milo)', 'linkOptions' => ['class' => 'logout']]
//            )
        ]
    ]);
    NavBar::end();
    ?>
    
    <div class="container content">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Content Motive <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
