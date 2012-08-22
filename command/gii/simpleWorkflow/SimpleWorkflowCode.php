<?php
/**
 * Implements the code model to generate workflow definition files suitable to
 * be used by the simpleWorkflow Extension for Yii.
 */
class SimpleWorkflowCode extends CCodeModel
{

    public $yedfile_upload;
    /**
     * @var string default workflow name
     */
    public $workflowName='Workflow';
    /**
     * @var string default path alias where generated workflow files are copied
     */
    public $workflowPath='application.models';
    /**
     * @var string path alias used to store uploaded file being processed
     */
    public $workPath='application.runtime';
    /**
     * @var boolean enable internationalization output for some specific workflow attributes
     * (be default, only the 'label' attribute can be internationalized)
     */
    public $enableI8N=false;
    /**
     * @var string default message translation category
     */
    public $messageCategory='workflow';
    
    /////////////////////////////////////////////////////////////////////////////////////////////////
    //
    
    private $_workflow=array();		// the generated workflow definition
    private $_workingFile;			// current uploaded working file
    
    /**
     * (non-PHPdoc)
     * @see CCodeModel::rules()
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
        	array('workflowPath', 'validateWorkflowPath'),
        	array('workflowPath', 'sticky'),
        	array('workflowName', 'required'),
        	array('workflowName, messageCategory', 'match', 'pattern'=>'/^\w+$/', 'message'=>'{attribute} should only contain word characters.'),
			array('messageCategory','validateMessageCategory'),
        	array('yedfile_upload,enableI8N', 'safe'),
        ));
    }
    /**
     * Checks that the workflow path is a valid existing folder.
     *
     * @param string $attribute
     * @param array $params
     */
    public function validateWorkflowPath($attribute,$params)
    {
    	if($this->hasErrors('workflowPath'))
    		return;
    	if(Yii::getPathOfAlias($this->workflowPath)===false)
    		$this->addError('workflowPath','Workflow Path must be a valid path alias.');
    }
    /**
     * Checks that is internationalization is enabled, a message category is provided
     *
     * @param string $attribute
     * @param array $params
     */
    public function validateMessageCategory($attribute,$params)
    {
    	if($this->enableI8N == '1' && (!isset($this->messageCategory) || $this->messageCategory == ''))
    		$this->addError('messageCategory','The message category can\'t be empty if internationalisation is enabled');
    }
    /**
     * (non-PHPdoc)
     * @see CCodeModel::attributeLabels()
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'workflowPath' 		=> 'Workflow Path',
        	'workflowName'  	=> 'Workflow Name',
        	'yedfile_upload' 	=> 'yEd Workflow File',
        	'enableI8N' 		=> 'Enable Internationalisation'
        ));
    }

	public function getWorkFilename(){
		return $this->_workingFile;
	}
	public function getWorkflowDefinition() {
		return $this->_workflow;
	}
	
    /**
     * To preserve the uploaded file during code generation without having to upload a new file
     * the uploaded file is saved and it is used during the current session, unless a new file is
     * uploaded.
     *
     * (non-PHPdoc)
     * @see CCodeModel::prepare()
     */
    public function prepare()
    {
    	$file = CUploadedFile::getInstance($this,'yedfile_upload');
    	
    	if( $file != null )
    	{
    		$this->_workingFile  = Yii::getPathOfAlias($this->workPath).'/'.$file->name;
    		$file->saveAs($this->_workingFile);
    		Yii::app()->user->setState('wfile', $this->_workingFile);
    	}
    	else
    	{
    		// the working file may have been previously uploaded
    		$this->_workingFile  = Yii::app()->user->getState('wfile');
    	}
    	
    	if( ! file_exists($this->_workingFile))
    	{
    		$this->_workingFile  = null;
    		Yii::app()->user->setState('wfile', null);
    		$this->_workflow = array();
    	}
    	else
    	{
    		// perform conversion and creates the simpleWorkflow definition array
    		
    		$converter = new SWyEdConverter();
    		$this->_workflow =  $converter->convert($this->_workingFile);
    	}
    	
        $path=Yii::getPathOfAlias($this->workflowPath).'/' . ucfirst($this->workflowName) . '.php';
        $code=$this->render($this->templatepath.'/include.php');
        $this->files[]=new CCodeFile($path, $code);
    }
    /**
     * Helper method to create code strings.
     *
     * @param string $str string to output
     * @param boolean $i8n if TRUE, the string is wrapped into a Yii::t() call
     */
	public function outputString($str,$i8n=false){
		$str = "'".str_replace("'","\\'",$str)."'";
		$i8n = $this->enableI8N && $i8n;
		return ($i8n
			? "Yii::t('".$this->messageCategory."',".$str.")"
			: $str
		);
	}
	/**
	 * (non-PHPdoc)
	 * @see CCodeModel::successMessage()
	 */
	public function successMessage(){
		return '<p>The Workflow has been generated successfully.</p>';
	}
}
?>