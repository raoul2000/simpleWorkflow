<?php
$w = $this->getWorkflowDefinition();
echo '<?php'; ?>
 
////////////////////////////////////////////////////////////////////////////////////////
// This simpleWorkflow definition file was generated automatically
// from a yEd Graph Editor file (.graphml).
//
// Workflow Name : <?php echo $this->workflowName; ?>

// Created       : <?php echo date('d/m/Y H:i');?>


class <?php echo ucfirst($this->workflowName); ?> {
<?php foreach($w['node'] as $node):?>
	const <?php echo 'STATUS_'.strtoupper($node['id']) ?> = <?php echo $this->outputString($node['id']).";\n"; ?>
<?php endforeach;?>

	public function getDefinition(){
		return array(
			'initial' => <?php echo 'self::STATUS_'.strtoupper($w['initial']);?>,
			'node'    => array(
<?php foreach($w['node'] as $node):?>
				array(
					'id' => <?php echo 'self::STATUS_'.strtoupper($node['id']);?>,
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
	}
}
