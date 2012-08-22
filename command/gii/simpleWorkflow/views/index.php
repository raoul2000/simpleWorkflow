<h1>SimpleWorkflow Generator : yEd Editor</h1>
<p>This generator converts a workflow created by <a href="http://www.yworks.com/en/products_yed_download.html" target="yed" >yEd Graph Editor</a>,
into a workflow ready to be used by your <a href="http://www.yiiframework.com/extension/simpleworkflow/" target="sw">simpleWorkflow extension</a>.</p>
<p><b>Before using this generator, make sure you have installed the SimpleWorkflow Extension.</b></p>

<?php
	$form=$this->beginWidget('CCodeForm',
		array(
			'model'       => $model,
			'htmlOptions' => array('enctype' => 'multipart/form-data')
		)
	);
?>
	<?php echo $form->errorSummary($model); ?>
	<div class="row">
	    <?php
	     	echo $form->labelEx($model, 'yedfile_upload');
	     	echo $form->fileField($model, 'yedfile_upload', array('size'=>50));
	     ?>
	    <div class="tooltip">
	    	Upload the workflow saved from <b>yEd Graph Editor</b> in .graphml format
	    </div>
     </div>
     <?php if( $model->getWorkFilename() != null):?>
	     <div class="row" style="margin-bottom: 20px;margin-top: 20px;">
	     	Currently Working on file <span style="background-color:yellow;font-weight: bold;"> <?php echo Chtml::encode($model->getWorkFilename()); ?> </span>
	     </div>
     <?php endif;?>
	<div class="row">
	    <?php
	     	echo $form->labelEx($model, 'workflowName');
	     	echo $form->textField($model,'workflowName', array('size'=>65));
	     ?>
	    <div class="tooltip">
	    	Once converted, the workflow definition file will be named with this value floowed by the '.php' extension
	    </div>
	     
     </div>
	<div class="row">
	    <?php
	     	echo $form->labelEx($model, 'enableI8N');
	     	echo $form->checkBox($model,'enableI8N');
	     ?>
	    <div class="tooltip">
	    	When checked, the 'label' attribute for each node will be wrapped with the
	    	Yii::t() internationalisation function.
	    </div>
     </div>
	<div class="row sticky">
		<?php
			echo $form->labelEx($model,'messageCategory');
			echo $form->textField($model,'messageCategory', array('size'=>65));
		?>
		<div class="tooltip">
			When internationalisation is enabled, this value is used as the message category for the
			call to <b>Yii:t()</b>. For instance if you set it to 'myMessages', the following code
			is generated :<code>Yii::t('myMessage','node label');</code>
		</div>
	</div>
	<div class="row sticky">
		<?php
			echo $form->labelEx($model,'workflowPath');
			echo $form->textField($model,'workflowPath', array('size'=>65));
		?>
		<div class="tooltip">
			This refers to the directory that the new workflow file should be generated under.
			It should be specified in the form of a path alias, for example, <code>application.models</code>,
			<code>mymodule.models.workflows</code>.
            <br /><br />When running the generator on Mac OS, Linux or Unix, you may need to change the
			permission of the script path so that it is full writeable. <br />Otherwise you will get a generation error.
		</div>
	</div>

<?php $this->endWidget(); ?>