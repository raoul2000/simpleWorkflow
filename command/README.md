The simpleWorkflow extension is released with a Gii command that assists you in the process of creating a new workflow.
Workflow creation can sometimes be an error-prone painful task, in particular if your workflow has more than 5 nodes !

In order to easily design a workflow, it is a good thing to use a graphical tool and the simpleWorkflow extension has chosen
the [yEd Graph Editor](http://www.yworks.com/en/products_yed_about.html) which is a really powerful application ... and it's free !

**The simpleWorkflow Gii Command is able to convert a workflow created by yEd and saved into *graphml* format, into a
workflow definition file that can be directly used by the simpleWorkflow Extension**.


#Requirements
* Yii 1.1.10 or above (never tested with previous Yii versions)
* make sure that Gii is correctly configured for your webapp
* install the **simpleWorkflow Extension**
* download and install yEd Graph Editor  

#Installation
* copy the `gii` folder into your `protected` folder of your webapp

#Usage
##Creating a workflow with yEd
We will not create a workflow from scratch but use the **workflow template file** provided with the extension. This is because our yEd workflow
needs to define some custom attributes that will be handled by the simpleWorkflow extension (e.g contraints, tasks, metadata, etc.).

* launch yEd Graph Editor
* open the workflow template file is located in `extension/simpleWorkflow/command/sW-yEd-template.graphml`
* to preserve this template, imediatly **save as ...** your workflow under a new name.
* start creating your workflow. Following fields are mandatory :  
	* node id must be entered in the General/Text field of the Property Panel
	* initial node id for your workflow must be entered in the Data/Initial-node-id field of the property panel. To display this field, make sure no item (node nor edge) is selected. 
* optionnally, you can enter value for following attributes in the Property Panel
	* node constraint : Data/Constraint
	* node label : Data/Label
	* node background color : General/Fill Color
	* node text color : Label/Color
	* edge task : Data/Task
* save your workflow into a graphml formated file

##Using the Gii simpleworkflow Command
Launch your webapp and go to the main Gii page. If the installation is correct, you should see the **simpleWorkflow Generator** command in the list of installed Gii commands. Click on it !
* upload the yEd file saved earlier using the yEd Workflow field
* choose a name for your workflow
* select the code template to use
	* default : this is the standard array format definition
	* class : (experimental) the workflow is defined as a class wit status id as constants.
* click on preview and if the generated workflow seems ok to you, click on 'Generate'.




Copyright (c) Raoul All rights reserved.
Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer. Redistributions in binary form must 
reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution. 
Neither the name of Raoul nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY 
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT 
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE 
