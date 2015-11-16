<?php 

class DataCompare {
	
	private $entityName;
	private $entityID;
	private $primaryKey;
	private $excludeFields;
	private $old;
	private $new;
	private $diffs;
	
	public function __construct() {
		$this->excludeFields = array(
			'create',
			'modified'
		);
	}
	
	public function setEntityName($entityName) {
		$this->entityName = $entityName;
	}
	
	public function setOld($old) {
		$this->old = $old;
	}
	
	public function setNew($new) {
		$this->new = $new;
	}
	
	public function setExcludeFields($excludeFields) {
		if (is_string($excludeFields)) {
			array_push($this->excludeFields, $excludeField);
		} else if (is_array($excludeFields)) {
			$this->excludeFields = array_merge($this->excludeFields, $excludeFields);
		}
	}
	
	public function setPrimaryKey($primaryKey) {
		$this->primaryKey = $primaryKey;
	}
	
	public function doDiff() {
		$this->old = $this->reKeyArray($this->old, $this->primaryKey);
		$this->new = $this->reKeyArray($this->new, $this->primaryKey);
		return $this->diff($this->old, $this->new);
	}
	
	public function diff($oldData, $newData) {
		$diff = array();
		if (empty($oldData)) {
			$oldData = array();
		}
		if (empty($newData)) {
			$newData = array();
		}
		$merge = $oldData + $newData;
		$keys = array_keys($merge);
	
		foreach ($keys as $field) {
			$old = array_key_exists($field, $oldData) ? $oldData[$field] : null;
			$new = array_key_exists($field, $newData) ? $newData[$field] : null;
			if ($old == $new) {
				$row = array('old' => '', 'new' => '', 'same' => $old);
			} else {
				$row = array('old' => $old, 'new' => $new, 'same' => '');
			}
			$diff[$field] = $row;
		}
		return $diff;
	}
	
	private function reKeyArray($array, $keyName) {

		if (empty($array) || empty($keyName)) {
			return null;
		}
		
		$newArray = array();
		foreach ($array as $item) {
			if (!array_key_exists($keyName, $item) && is_array($item)) {
				foreach ($item as $child) {
					if (array_key_exists($keyName, $child) && is_null($child[$keyName])) {
						$id = uniqid();
						$newArray[$id] = $this->removeExcludeKeys($child);
					} else {
						$newArray[$child[$keyName]] = $this->removeExcludeKeys($child);
					}
				}
			} elseif (is_null($item[$keyName])) {
				$id = uniqid();
				$newArray[$id] = $this->removeExcludeKeys($item);
			} else {
				$newArray[$item[$keyName]] = $this->removeExcludeKeys($item);
			}
		}
		return $newArray;
	}
	
	private function removeExcludeKeys($item) {
		$excludedFields = array_flip($this->excludeFields);
		if (is_array($item) && is_array($excludedFields)) {
			$item = array_diff_key($item, $excludedFields);
		}
		return $item;
	}
}
