<?php

/**
 * this class provides helper methods for the simpleWorkflow behavior
 */
class SWHelper
{
	/**
	 * Returns the list of all statuses that can be reached from current status of the model
	 * passed as argument. The returned array is in the form suitable for dropDownList and listBox:
	 * <pre>
	 *    array(
	 *        'statusId' => 'status label',
	 *        'status Id2' => 'status label 2',
	 *        etc ...
	 *    )
	 * </pre>
	 * Use the $options argument to specify following options :
	 * <ul>
	 * <li><b>prompt</b> : specifies the prompt text shown as the first list option. Its value is empty.
	 * Note, the prompt text will NOT be HTML-encoded</li>
	 * <li><b>includeCurrent</b> : boolean, if TRUE (default) the current model status is included in the list,
	 * otherwise current model status is not inserted in the returned array.</li>
	 * <li><b>exclude</b> : array, list of statuses that should not be inserted in the returned array</li>
	 * </ul>
	 * Note that each status label is html encode by default.
	 *
	 * @param CModel $model the data model attaching a simpleWorkflow behavior
	 * @param array $options additional options
	 * @return array the list data that can be used in dropDownList and listBox
	 */
	public static function nextStatuslistData($model, $options = array())
	{
		return SWHelper::_createListData($model, $model->swGetNextStatus(), $options);
	}

	/**
	 * Returns the list of all statuses belonging to the workflow the model passed as argument
	 * is in.
	 * see {@link SWHelper::nextStatuslistData} for argument options
	 *
	 * @param CModel $model the data model attaching a simpleWorkflow behavior
	 * @param array $options additional options
	 * @return array the list data that can be used in dropDownList and listBox
	 */
	public static function allStatuslistData($model, $options = array())
	{
		return SWHelper::_createListData($model, $model->swGetAllStatus(), $options);
	}

	/**
	 * Create an array containing where keys are statusIds in the form workflowId/statusId
	 * and the value is the status label.
	 * Note that by default this method never inserts the status of the model passed as argument.
	 * see {@link SWHelper::nextStatuslistData} for argument options
	 *
	 * @param CModel $model the data model attaching a simpleWorkflow behavior
	 * @param array $statusList array of string where each value is the statusId
	 * @param array $options the list data that can be used in dropDownList and listBox
	 * @return array
	 */
	public static function statusListData($model, $statusList, $options = array())
	{
		$nodeList = array();
		$w = $model->swGetWorkflowSource();

		foreach ($statusList as $statusId) {
			$nodeList[] = $w->getNodeDefinition($statusId);
		}

		$options['includeCurrent'] = (isset($options['includeCurrent'])
			? $options['includeCurrent']
			: false
		);

		return SWHelper::_createListData($model, $nodeList, $options);
	}

	/**
	 * Returns an array where keys are status id and values are status labels.
	 *
	 * @param CModel $model
	 * @param array $statusList SWNode list
	 * @param array $options (optional)
	 * @return array
	 * @throws CException
	 */
	private static function _createListData($model, $statusList, $options = array())
	{
		$result = array();
		$exclude = null;
		$includeCurrent = true;

		$currentStatus = ($model->swHasStatus()
			? $model->swGetStatus()
			: null
		);

		if ($currentStatus !== null)
			$result[$currentStatus->toString()] = $currentStatus->getLabel();

		$encodeLabel = (isset($options['encode'])
			? (bool)$options['encode']
			: true
		);

		/**
		 * process options
		 */
		if (count($options)) {
			if (isset($options['prompt'])) {
				$result[''] = $options['prompt'];
			}

			if (isset($options['exclude'])) {
				if (is_string($options['exclude']))
					$exclude = array_map('trim', explode(",", $options['exclude']));
				elseif (is_array($options['exclude']))
					$exclude = $options['exclude'];
				else
					throw new CException('incorrect type for option "exclude" : array or string expected');

				foreach ($exclude as $key => $value) {
					$node = new SWNode($value, $model->swGetWorkflowId());
					$exclude[$key] = $node->toString();
				}
			}

			if (isset($options['includeCurrent']))
				$includeCurrent = (bool)$options['includeCurrent'];

			if ($exclude !== null && $currentStatus !== null && in_array($currentStatus->toString(), $exclude))
				$includeCurrent = false;
		}

		if (count($statusList)) {
			foreach ($statusList as $nodeObj) {

				if ($exclude === null || ($exclude !== null && !in_array($nodeObj->toString(), $exclude))) {
					$result[$nodeObj->toString()] = ($encodeLabel
						? CHtml::encode($nodeObj->getLabel())
						: $nodeObj->getLabel()
					);
				}
			}
		}

		if ($includeCurrent === false && $currentStatus !== null) {
			unset($result[$currentStatus->toString()]);
		}

		return $result;
	}
}