<?php
$w = $this->getWorkflowDefinition();
echo '<?php'; ?>
 
////////////////////////////////////////////////////////////////////////////////////////
// This simpleWorkflow definition file was generated automatically
// from a yEd Graph Editor file (.graphml).
//
// Workflow Name : <?php echo $this->workflowName; ?>

// Created       : <?php echo date('d/m/Y H:i');?>


return array(
	'initial' => <?php echo $this->outputString($w['initial']);?>,
	'node'    => array(
<?php foreach($w['node'] as $node):?>
		array(
			'id' => '<?php echo $node['id'];?>',
<?php if( isset($node['label'])):?>
			'label' => <?php echo $this->outputString($node['label'],true).",\n";	?>
<?php endif;?>
<?php if( isset($node['constraint'])):?>
			'constraint' => <?php echo $this->outputString($node['constraint']).",\n";	?>
<?php endif;?>
<?php if( isset($node['transition'])):?>
			'transition' => array(
<?php foreach($node['transition'] as $target => $transition):?>
				<?php
					if( is_string($target)){
						echo "'".$target."' => ";
					}
					echo $this->outputString($transition).",\n";
				?>
<?php endforeach;?>
			),
<?php endif;?>
<?php if( isset($node['metadata'])):?>
			'metadata' => array(
<?php foreach($node['metadata'] as $name => $value):?>
				<?php echo $this->outputString($name).' => '.$this->outputString($value).",\n";	?>
<?php endforeach;?>
			),
<?php endif;?>
		),
<?php endforeach;?>
	)
);
