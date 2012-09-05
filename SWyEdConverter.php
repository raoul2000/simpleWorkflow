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
class SWyEdConverter {

	/**
	 * @var array mapper between SW Workflow attributes and yEd attributes
	 */
	public $mapper = array(
		'workflow' => array(
			'id'      => 'Id',					// optional
			'initial' => 'Initial-node-id',		// mandatory
		),
		'node' => array(
			'id' 				=> 'xpath|y:ShapeNode/y:NodeLabel',					// mandatory
			'constraint' 		=> 'Constraint',									// optional
			'label' 			=> 'Label',											// optional
			'metadata.color' 	=> 'xpath|y:ShapeNode/y:NodeLabel/@textColor',		// optional
			'metadata.bgcolor' 	=> 'xpath|y:ShapeNode/y:Fill/@color',				// optional
		),
		'edge' => array(
			'task' => 'Task'														// optional
		)
	);
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// private members
	
	private $_xml;
	private $_yedProperties;
	private $_workflow = array(
		'workflow' => array(),
		'node'     => array()
	);
	/**
	 *
	 * @param string $file name of th graphml (xml) file to process
	 * @return array the workflow definition
	 */
	public function convert($file)
	{
		if(!extension_loaded('domxml')){
			throw new SWException('extension domxml not loaded : yEd converter requires domxml extension to process');
		}
		// reset all
		$this->_xml           = null;
		$this->_yedProperties = array();
		$this->_workflow      = array(
			'workflow' => array(),
			'node'     => array()
		);
				
		$this->_xml = simplexml_load_file($file);
		$namespaces = $this->_xml->getNamespaces(true);
		
		$this->_xml->registerXPathNamespace('y', 'http://www.yworks.com/xml/graphml');
		$this->_xml->registerXPathNamespace('__empty_ns', $namespaces['']);
		
		// extract yEd custom attributes IDs ///////////////////////////////////////////////////////////////////
		//
		// for instance : <key attr.name="description" attr.type="string" for="node" id="d7"/>
		// is turned into
		// array(
		//	'node' => array(
		//		'description' => 'd7',
		//      'nodegraphics' => 'd9',
		//		etc...
		//	)
		//
		
		$nlKey = $this->_xml->xpath('/__empty_ns:graphml/__empty_ns:key');

		foreach($nlKey as $ndKey){
		
			if( $ndKey['attr.name'] != null &&
				( $ndKey['for'] == 'node' ||  $ndKey['for'] == 'edge' || $ndKey['for'] == 'graph'))
			{
				$for      = (string) $ndKey['for'];
				$attrName = (string) $ndKey['attr.name'];
		
				if( ! isset($this->_yedProperties[$for]) ) 	$this->_yedProperties[$for] = array();
		
				$this->_yedProperties[$for][$attrName] = (string) $ndKey['id'];
			}
			elseif(  $ndKey['yfiles.type'] == 'nodegraphics' ||  $ndKey['yfiles.type'] == 'edgegraphics' )
			{
				$for      = (string )$ndKey['for'];
				$fileType = (string) $ndKey['yfiles.type'];
				
				$this->_yedProperties[$for][$fileType] = (string) $ndKey['id'];
			}
		}
		$nodeGraphicId = $this->_yedProperties['node']['nodegraphics'];
		
		// extract workflow properties /////////////////////////////////////////////////////

		$nlGraph    = $this->_xml->xpath('//__empty_ns:graph');
		$yedGraphId = (string) $nlGraph[0]['id'];
		
		foreach($this->mapper['workflow'] as $swAttrName => $yedAttrName)
		{
			// echo '<li>Extracting attribute sw:'.$swAttrName.' from yed:'.$yedAttrName.'</li>';
		
			// creates the XPath that is applied to the yEd XML file in order to retrieve
			// value for simpleWorkflow attribute $swAttrName (and for the current workflow)
		
			if( preg_match('/^xpath\|(.*)$/',$yedAttrName,$matches))
			{
				$xpath = '//__empty_ns:graph[@id="'.$yedGraphId.'"]/__empty_ns:data[@key="'.$nodeGraphicId.'"]/'.$matches[1];
			}
			elseif( isset( $this->_yedProperties['graph'][$yedAttrName]))
			{
				$yedDataKey =  $this->_yedProperties['graph'][$yedAttrName];
				$xpath = '//__empty_ns:graph[@id="'.$yedGraphId.'"]/__empty_ns:data[@key="'.$yedDataKey.'"]';
			}
			else{
				continue;
			}
		
			// // echo '<li>evaluating xpath = '.$xpath.' : ';
			$result = $this->_xml->xpath($xpath);
			if( count($result) == 1 ){
				// // echo 'value found = <u>'.$result[0].'</u>';
				$this->_workflow['workflow'][$swAttrName] = trim((string) $result[0]);
			}
			else {
				//$this->_workflow['workflow'][$swAttrName] = null;
				// // echo 'WARNING : value not found';
			}
			// // echo '</li>';
		}
		// // echo '</ul>';
		// // echo '<pre>'.CVarDumper::dumpAsString($this->_workflow['workflow']).'</pre>';

		// extract nodes ///////////////////////////////////////////////////////////////////
		
		// // echo '<h2>Extracting Nodes</h2>';
		
		$nlNode = $this->_xml->xpath('//__empty_ns:node');
		foreach($nlNode as $ndNode)
		{
			$yNodeId = (string) $ndNode['id'];
			$this->_workflow['node'][$yNodeId] = array();
			// // echo 'yEd node Id = '.$yNodeId.'<br/>';
		
			foreach($this->mapper['node'] as $swAttrName => $yedAttrName)
			{
		
				// // echo 'maping (sw)'.$swAttrName.' &lt;- (yEd)'.$yedAttrName.'<br/>';
		
				// creates the XPath that is applied to the yEd XML file in order to retrieve
				// value for simpleWorkflow attribute $swAttrName (and for the current node)
		
				if( preg_match('/^xpath\|(.*)$/',$yedAttrName,$matches))
				{
					$relXpath = $matches[1];
					$xpath = '//__empty_ns:node[@id="'.$yNodeId.'"]/__empty_ns:data[@key="'.$nodeGraphicId.'"]/'.$relXpath;
				}
				elseif( isset( $this->_yedProperties['node'][$yedAttrName]))
				{
					$yedDataKey =  $this->_yedProperties['node'][$yedAttrName];
					$xpath = '//__empty_ns:node[@id="'.$yNodeId.'"]/__empty_ns:data[@key="'.$yedDataKey.'"]';
				}else{
					continue;
				}
		
				// XPath could be created : evaluate it now and store the returned value as a string
		
				// echo 'evaluating xpath = '.$xpath.'<br/>';
				$result = $this->_xml->xpath($xpath);
				if( count($result) == 1 ){
					// echo 'value found = '.$result[0].'<br/>';
					$this->_workflow['node'][$yNodeId][$swAttrName] = trim((string) $result[0]);
				}
				else {
					//$this->_workflow['node'][$yNodeId][$swAttrName] = null;
					// echo 'WARNING : value not found<br/>';
				}
			}
			// echo '<pre>'.CVarDumper::dumpAsString($this->_workflow['node'][$yNodeId]).'</pre>';
			// echo '<hr/>';
		}

		// process edges ///////////////////////////////////////////////////////////////////
		
		// echo '<h2>Extracting Edges</h2>';
		
		$nlEdge = $this->_xml->xpath('//__empty_ns:edge');
		
		foreach($nlEdge as $ndEdge)
		{
			$yedSource = (string) $ndEdge['source'];
			$yedTarget = (string) $ndEdge['target'];
			$yEdgeId   = (string) $ndEdge['id'];
		
			// echo 'processing edge from '.$yedSource.' to '.$yedTarget.'<br/>';
		
			if( isset($this->_workflow['node'][$yedSource]) && isset($this->_workflow['node'][$yedTarget]))
			{
				// echo 'sw nodes found<br/>';
				$swNodeSource = & $this->_workflow['node'][$yedSource];
				$swNodeTarget = & $this->_workflow['node'][$yedTarget];
		
				if(!isset($swNodeSource['transition'])){
					$swNodeSource['transition'] = array();
				}
		
				// is there a task for this transition ? Task is the only attribute
				// that can be attached to a transition
		
				$yedAttrName = $this->mapper['edge']['task'];
		
				if( preg_match('/^xpath\|(.*)$/',$yedAttrName,$matches))
				{
					$relXpath = $matches[1];
					$xpath = '//__empty_ns:edge[@id="'.$yEdgeId.'"]/__empty_ns:data[@key="'.$nodeGraphicId.'"]/'.$relXpath;
				}
				elseif( isset( $this->_yedProperties['edge'][$yedAttrName]))
				{
					$yedDataKey =  $this->_yedProperties['edge'][$yedAttrName];
					$xpath = '//__empty_ns:edge[@id="'.$yEdgeId.'"]/__empty_ns:data[@key="'.$yedDataKey.'"]';
				}else{
					continue;
				}
		
				// XPath could be created : evaluate it now and store the returned value as a string
		
				// echo 'evaluating xpath = '.$xpath.'<br/>';
				$result = $this->_xml->xpath($xpath);
				if( count($result) == 1 ){
					// echo 'value found = '.$result[0].'<br/>';
					$swNodeSource['transition'][$swNodeTarget['id']] = trim((string) $result[0]);
				}
				else {
					$task = null;
					// echo 'WARNING : value not found<br/>';
					$swNodeSource['transition'][] = $swNodeTarget['id'];
				}
			}
		}
		
		// echo '<h2>Raw Workflow Array</h2>';
		// echo '<pre>'.CVarDumper::dumpAsString($this->_workflow).'</pre>';
		
		// normalize workflow array ///////////////////////////////////////////////////////////////////
		
		// echo '<h2>Normalize Workflow Array</h2>';
		
		if(isset($this->_workflow['workflow']['initial'])){
			$this->_workflow['initial'] = $this->_workflow['workflow']['initial'];
		}

		unset($this->_workflow['workflow']);
		
		foreach ($this->_workflow['node'] as $key => $node)
		{
			$normalizedNode = array();
			foreach ($node as $nodeAttrName => $nodeAttrValue)
			{
				if( preg_match('/^metadata\.(.*)$/',$nodeAttrName,$matches))
				{
					if(!isset($normalizedNode['metadata'])){
						$normalizedNode['metadata'] = array();
					}
					// echo 'metadata : '.$mdAttr.' &larr; '.$nodeAttrValue.'<br/>';
					$normalizedNode['metadata'][$matches[1]] = $nodeAttrValue;
				}else {
					$normalizedNode[$nodeAttrName] = $nodeAttrValue;
				}
			}
			unset($this->_workflow['node'][$key]);
			$this->_workflow['node'][] = $normalizedNode;
		}
		return $this->_workflow;
	}
}