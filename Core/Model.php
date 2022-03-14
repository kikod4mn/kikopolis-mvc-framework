<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Kikopolis\Model\Exception\ModelValidationException;
use Throwable;

abstract class Model {
	protected array $validations = [];
	protected array $errors      = [];
	
	public function getErrors(): array {
		return $this->errors;
	}
	
	public function addError(ModelValidationException $error): void {
		$this->errors[] = $error;
	}
	
	public function getValidations(): array {
		return $this->validations;
	}
	
	public function isValid(): bool {
		if (empty($this->errors) === false) {
			return false;
		}
		return true;
	}
	
	public function store(): bool {
		return false;
	}
	
	public static function create(array $data): self {
		try {
			$model = new static(...$data);
		} catch (Throwable $ex) {
			throw new $ex;
		}
		foreach ($model->validations as $validation) {
			try {
				$validation->validate();
			} catch (ModelValidationException $e) {
				$model->addError(new $e());
			}
		}
		return $model;
	}
}