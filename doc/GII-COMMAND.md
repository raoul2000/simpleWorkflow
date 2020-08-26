- [Workflow Creation](#workflow-creation)
- [Gii command + yEd Graph Editor](#gii-command--yed-graph-editor)
  - [Installation](#installation)
  - [Creating a workflow with yEd](#creating-a-workflow-with-yed)
  - [Conversion with Gii](#conversion-with-gii)
  - [Code templates](#code-templates)

# Workflow Creation

*An easy way to create workflows for the simpleWorkflow extension*

[Check this great video](http://player.vimeo.com/video/48693938)

# Gii command + yEd Graph Editor

Creating a workflow 'by hand' can become an error-prone task when several nodes and edges are required. One good option is to create the workflow using a visual tools, and after some searches it seems that one of the best (and free) application to do it is yEd Graph Editor.

Once created and saved format, the workflow can be converted into a simpleWorkflow, ready to be used by the extension.

In this video you'll see how easy it is to :

- create a workflow with yEd, based on the simpleWorkflow template provided by the extension
- save the workflow as a `graphml` file
- use the Gii command to convert the graphml file into a PHP file that contains your worklow ready to be used by simpleWorkflow

## Installation

To be able to create your workflow using yEd Graph Editor you must :

- Make sure you have downloaded an correctly installed simpleWorkflow
- [Download and install](http://www.yworks.com/en/products_yed_download.html) the latest version of the yEd Graph Editor
- Install the simpleWorkflow Gii Command. Just copy the `simpleWorkflow/command/gii` folder to your protected folder
- Make sure that the Gii module is enabled in your `protected/config/main.php` file

## Creating a workflow with yEd

To create our workflow with yEd we use the workflow template provided with the extension. This is because This is because our yEd workflow needs to define some custom attributes that will be handled by the simpleWorkflow extension (e.g contraints, tasks, metadata, etc.).

- launch yEd Graph Editor
- open the workflow template file `/sW-yEd-template.graphml` located in `extension/simpleWorkflow/command`
- to preserve this template, imediatly save as ... your workflow under a new name.
- start creating your workflow

Using yEd and the workflow template, you are able to set properties which are needed by the simpleWorkflow extension.
There are properties for the workflow itself, for nodes and also for edges (transitions). 

Below is the list of all simpleWorkflow properties available in yEd :

<table class="table table-striped">
    <thead>
    <tr>
        <th>property name</th>
        <th>object</th>
        <th>location</th>
        <th>description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><code>initial-node-id</code></td>
        <td>workflow</td>
        <td>properties/data/Initial-node-id</td>
        <td>Id of the node that is used to enter into this workflow.<br/>
        <span class="label label-important">mandatory property</span></td>
    </tr>
    <tr>
        <td><code>node-id</code></td>
        <td>node</td>
        <td>properties/general/Text</td>
        <td>Id of the node.<br/>
        <span class="label label-important">mandatory property</span></td>
    </tr>  
    <tr>
        <td><code>constraint</code></td>
        <td>node</td>
        <td>properties/data/constraint</td>
        <td>PHP expression evaluated as a node entry condition</td>
    </tr>      
    <tr>
        <td><code>label</code></td>
        <td>node</td>
        <td>properties/data/label</td>
        <td>Status display name</td>
    </tr>       
    <tr>
        <td><code>background-color</code></td>
        <td>node</td>
        <td>General/Fill Color</td>
        <td>stored as node metadata, this value can be used to render the background color of a node (if needed)</td>
    </tr>   
    <tr>
        <td><code>text-color</code></td>
        <td>node</td>
        <td>Label/Color</td>
        <td>stored as node metadata, this value can be used to render the text color of a node (if needed)</td>
    </tr>
    <tr>
        <td><code>task</code></td>
        <td>edge</td>
        <td>properties/data/Task</td>
        <td>PHP expression evaluated when the transition is done</td>
    </tr>                
    </tbody>
</table>                    

When you're done, save your workflow into a graphml formated file

**warning**: it is important to select the correct file format when saving your workflow, otherwise the conversion to simpleWorkflow format will fail.

## Conversion with Gii

Now that your yEd workflow is saved into a graphml file, we must convert it into a simpleWorkflow format.
To do so, select simpleWorkflow Generator from the main Gii page, and upload your workflow.

The last part is to complete de form and choose a code template.

## Code templates

Two code templates are available :

- **default** : the workflow will be saved as an associative array
- **class** : the workflow is saved as a PHP class that include the definition itself. Using a class to hold a workflow definition requires a dedicated worklow source component with its definitionType property initialised to class. Check SWPhpWorkflowSource API for more on that.

