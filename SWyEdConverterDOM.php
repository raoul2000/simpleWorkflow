<?php
/**
 * Converts a workflow created with yEd Graph Editor (freeware) and saved in graphml format
 * into an array suitable to be used with the simpleWorkflow extension (sW).<br/>
 * The conversion is based on the $mapper array, which defines matches between sW attributes
 * and properties used by yEd.
 * Following sW values are references using a predefined name :
 * <ul>
 * 		<li>workflow - initial : is the id of the initial node</li>
 * 		<li>node - id : id of a given node</li>
 * 		<li>node - constraint : PHP expression used as constraint for a node</li>
 * 		<li>node - label : text label for a node</li>
 * 		<li>node - metadata.* : custom metadata value</li>
 * 		<li>edge - task : PHP expression executed when the transition is performed</li>
 * </ul>
 * yEd Graph Editor node attributes are referenced in two ways : by their attribute name or by
 * an xpath expression that applies to the y:ShapeNode element used by yEd to draw each node.
 * The later is mainly usefule to extract the node label and possibly, some other informations
 * like for instance the background color or text color used to render each node. The former
 * is useful to extract built-in yEd attribute and also custom attribute defined by the user.<br/>
 *
 */
class SWyEdConverterDOM {
	/**
	 * Maps a named attribute with a id attribute defined in the input graphml file.
	 * @var array
	 */
	private $_mapper = array(
	// workflow
	'w-intial-node-id' => "/ns:graphml/ns:key[@for='graph'][@attr.name='Initial-node-id']/@id",

	// nodes
	'n-constraint'     => "/ns:graphml/ns:key[@for='node'][@attr.name='Constraint']/@id",
	'n-label'     	   => "/ns:graphml/ns:key[@for='node'][@attr.name='Label']/@id",
	'n-graphics'       => "/ns:graphml/ns:key[@for='node'][@yfiles.type='nodegraphics']/@id",

	// edges
	'e-task'		   => "/ns:graphml/ns:key[@for='edge'][@attr.name='Task']/@id",
	);
	/**
	 * @var DOMDocument XML DOM parser
	 */
	private $_dom;
	/**
	 * @var DOMXPath The XPath object used to evaluate all xpath expressions
	 */
	private $_xp;
	/**
	 * @var array map between named properties and graphml properties extracted from the input
	 * file. This array is builte based on the <em>_mapper</em> array.
	 */
	private $_yedProperties = array();
	/**
	 * Convert a graphml file describing a workflow into an array suitable to create a <em>simpleWorkflow</em>
	 * object.
	 * @param string $graphmlFile the path to the graphml file to process
	 */
	public function convert($graphmlFile) {

		$this->_dom = new DOMDocument();
		$this->_dom->load($graphmlFile);

		$this->_xp = new DOMXPath($this->_dom);
		$this->_xp->registerNamespace('ns','http://graphml.graphdrawing.org/xmlns');
		$this->_xp->registerNamespace('y','http://www.yworks.com/xml/graphml');

		$this->_extractYedProperties();

		$workflow = $this->_collectWorkflow();
		//echo '<b>Workflow : </b><pre>'.CVarDumper::dumpAsString($workflow).'</pre><hr/>';

		$nodes = $this->_collectNodes();
		//echo '<b>nodes : </b><pre>'.CVarDumper::dumpAsString($nodes).'</pre><hr/>';

		$edges = $this->_collectEdges();
		//echo '<b>Edges : </b><pre>'.CVarDumper::dumpAsString($edges).'</pre><hr/>';


		$result = $this->_createSwWorkflow($workflow, $nodes, $edges);
		//echo '<b>Result : </b><pre>'.CVarDumper::dumpAsString($result).'</pre><hr/>';
		return $result;

	}
	/**
	 * Merges all arrays extracted from the graphml file (workflow, nodes, edges) to create and
	 * return a single array descrbing a simpleWorkflow.
	 *
	 * @param array() $w workflow attributes
	 * @param array() $n nodes attributes
	 * @param array() $e edges attributes
	 */
	private function _createSwWorkflow($w,$n,$e){
		$nodes=array();

		foreach ($n as $key => $node) {
				
			$newNode = array(
			'id'         => $node['id'],
			'label'      => $node['label'],
			'constraint' => $node['constraint'],
			'metadata'   => array(
			'background-color' => $node['background-color'],
			'color'            => $node['color']
			)
			);
			if(isset($e[$key])){
				foreach ($e[$key] as $trgKey => $edge) {
					$newNode['transition'][$n[$trgKey]['id']] =  $edge;
				}
			}
				
			// normalize transitions
				
			if(isset($newNode['transition'])){
				foreach($newNode['transition'] as $targetId => $edge){
					if(count($edge) == 0){
						unset($newNode['transition'][$targetId]);
						$newNode['transition'][] = $targetId;
					}elseif(isset($edge['task'])){
						$newNode['transition'][$targetId] = $edge['task'];
					}
				}
			}
			$nodes[] = $newNode;
		}
		return  array(
		'initial' => $w['initial'],
		'node'    => $nodes
		);
	}
	/**
	 * Retrieve the graphml id attribute for each named properties defines in the <em>_mapper</em>
	 * array.
	 */
	private function _extractYedProperties() {

		foreach ($this->_mapper as $attrName => $xp) {
				
			$nodeList = $this->_xp->query($xp);
			if( $nodeList->length != 1){
				throw new CException("failed to extract id for attribute $attrName");
			}
				
			$this->_yedProperties[$attrName] = $nodeList->item(0)->value;
		}
	}
	/**
	 *
	 * @throws CException
	 */
	private function _collectWorkflow(){

		$nlGraph = $this->_xp->query('//ns:graph');
		if($nlGraph->length == 0)
			throw new CException("no workflow definition found");

		if($nlGraph->length > 1)
			throw new CException("more than one workflow found");

		// extract custom properties /////////////////////////////////////////////////////////////////
		// INITIAL

		$nl2 = $this->_xp->query('/ns:graphml/ns:graph/ns:data[@key="'.$this->_yedProperties['w-intial-node-id'].'"]');
		if($nl2->length!=1 || $this->_isBlank($nl2->item(0)->nodeValue) )
			throw new CException("failed to extract initial node id for this workflow");

		$result=array(
		'initial' => trim($nl2->item(0)->nodeValue)
		);

		return $result;
	}
	/**
	 * Extract edges defined in the graphml input file
	 * @return array()
	 * @throws CException
	 */
	private function _collectEdges(){

		$nlEdges = $this->_xp->query('//ns:edge');
		if($nlEdges->length == 0)
			throw new CException("no edge could be found in this workflow");

		$result=array();
		for($i=0; $i < $nlEdges->length; $i++)
		{
			$currentNode= $nlEdges->item($i);

			$source = trim($this->_xp->query("@source",$currentNode)->item(0)->value);
			$target = trim($this->_xp->query("@target",$currentNode)->item(0)->value);
				
			if(!isset($result[$source]) || !isset($result[$source][$target])){
				$result[$source][$target] = array();
			}
				
			// extract custom properties /////////////////////////////////////////////////////////////////
			// TASK
				
			$nl2 = $this->_xp->query('ns:data[@key="'.$this->_yedProperties['e-task'].'"]',$currentNode);
			if($nl2->length==1 && ! $this->_isBlank($nl2->item(0)->nodeValue))
				$result[$source][$target]['task'] = trim($nl2->item(0)->nodeValue);
		}
		return $result;
	}
	/**
	 * Extract nodes defined in the graphml input file.<br/>
	 * When working with yEd, remember that the node 'label' is used as the node id by simpleWorkflow. This is the only
	 * required value for a valid node. A node with no label in yEd will be ingored by this converter.
	 *
	 * @return array()
	 * @throws CException
	 */
	private function _collectNodes(){
		$nlNodes = $this->_xp->query('//ns:node');
		if($nlNodes->length == 0)
			throw new CException("no node could be found in this workflow");

		$result=array();
		for($i=0; $i < $nlNodes->length; $i++)
		{
			$currentNode = $nlNodes->item($i);
				
			$nl2 = $this->_xp->query("@id",$currentNode);
				
			if($nl2->length != 1)
				throw new CException("failed to extract yed node id");
				
			// yEd node Id
			$yNodeId = trim($nl2->item(0)->value);
				
			// extract mandatory properties ////////////////////////////////////////////////////////////
				
			$nl2 = $this->_xp->query('ns:data[@key="'.$this->_yedProperties['n-graphics'].'"]/y:ShapeNode/y:NodeLabel',$currentNode);
			if($nl2->length !=1)
				continue;
				
			$result[$yNodeId] = array();
			$result[$yNodeId]['id'] = trim($nl2->item(0)->nodeValue);

			// extract custom properties /////////////////////////////////////////////////////////////////
				
			$nl2 = $this->_xp->query('ns:data[@key="'.$this->_yedProperties['n-constraint'].'"]',$currentNode);
			if($nl2->length==1)
				$result[$yNodeId]['constraint'] = trim($nl2->item(0)->nodeValue);
				
			$nl2 = $this->_xp->query('ns:data[@key="'.$this->_yedProperties['n-label'].'"]',$currentNode);
			if($nl2->length==1)
				$result[$yNodeId]['label'] = trim($nl2->item(0)->nodeValue);
				
			$nl2 = $this->_xp->query('ns:data[@key="'.$this->_yedProperties['n-graphics'].'"]/y:ShapeNode/y:Fill/@color',$currentNode);
			if($nl2->length ==1)
				$result[$yNodeId]['background-color'] = trim($nl2->item(0)->nodeValue);
				
			$nl2 = $this->_xp->query('ns:data[@key="'.$this->_yedProperties['n-graphics'].'"]/y:ShapeNode/y:NodeLabel/@textColor',$currentNode);
			if($nl2->length ==1)
				$result[$yNodeId]['color'] = trim($nl2->item(0)->nodeValue);
		}
		return $result;
	}
	/**
	 * @param string $str
	 * @return boolean TRUE if the string passed as argument is null, empty, or made of space character(s)
	 */
	private function _isBlank($str){
		return !isset($str) || strlen(trim($str)) == 0;
	}
}