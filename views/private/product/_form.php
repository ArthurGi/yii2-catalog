<?php
use yii\helpers\Html;
use app\modules\privatepanel\Module as Module;
use app\modules\catalog\models\Product;
/* @var $model app\modules\catalog\models\Product */
$model = $privateModelAdmin->getModel();
?>
<?=Html::beginForm("", "POST", ['enctype' => 'multipart/form-data', 'class' => '']);?>
<table cellspacing="1" cellpadding="3" border="0" class="table table-striped table-editing">
  <thead>
    <tr>
        <th>&nbsp;</th>
        <th><?php echo Module::t('app', $model->isNewRecord ? 'Create' : 'Update')?></th>
    </tr>
  </thead>
  <tbody>
  <tr>
    <td><?=Html::activeLabel($model, 'name')?></td>
    <td>
      <?=Html::activeTextInput($model, 'name', ['class' => 'form-control', 'required' => true])?>
      <?=Html::tag('div', $model->getFirstError('name'), ['class' => 'form-error']);?>
    </td>
  </tr>
  <tr>
    <td><?=Html::activeLabel($model, 'alias')?></td>
    <td>
      <?=Html::activeTextInput($model, 'alias', ['class' => 'form-control'])?>
      <?=Html::tag('div', $model->getFirstError('alias'), ['class' => 'form-error']);?>
    </td>
  </tr>
  </tbody>
  <tfoot>
    <tr>
        <td>&nbsp;</td>
        <td>
        <?= Html::submitButton('<i class="fa fa-floppy-o"></i> '.Module::t('app', $model->isNewRecord ? 'Create' : 'Save'), ['class' => 'btn btn-success', 'name'=>'save']) ?>&nbsp;
        <?= Html::submitButton('<i class="fa fa-check-circle-o"></i> '.Module::t('app', 'Save & continue edit'), ['class' => 'btn btn-primary', 'onclick'=>'jQuery(this).closest("form").find("[name=\"savetype\"]").val("apply")', 'name'=>'apply']) ?>&nbsp;
        <?php echo Html::a('<i class="fa fa-times-circle-o"></i> '.Module::t('app', 'Cancel'), array_merge(['admin'], $privateModelAdmin->additionalUrlParams), ['class' => 'btn btn-gray']); ?>
        </td>
    </tr>
  </tfoot>
</table>
<?=Html::endForm();?>