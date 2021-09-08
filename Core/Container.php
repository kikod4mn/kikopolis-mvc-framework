<?php

declare(strict_types = 1);

namespace Kikopolis\Core;

use Kikopolis\Core\Collection\MixedCollection;
use Kikopolis\Core\Exception\DefaultValueNotAvailableException;
use Kikopolis\Core\Exception\NoServiceException;
use Kikopolis\Core\Exception\ServiceNotInstantiableException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use function class_exists;
use function count;
use function is_string;
use function sprintf;

final class Container {
	private MixedCollection $services;
	
	public function __construct() {
		$this->services = new MixedCollection(null, [
			Request::class => Request::class,
		]);
	}
	
	public function get(string $name): object {
		if (! $this->services->containsKey($name) || ! class_exists($name)) {
			throw new NoServiceException(sprintf('Service "%s" does not exist.', $name));
		}
		$fqn       = $this->services->get($name);
		$reflector = new ReflectionClass($fqn);
		if (! $reflector->isInstantiable()) {
			throw new ServiceNotInstantiableException(sprintf('Service "%s" is not instantiable.', $name));
		}
		$constructor = $reflector->getConstructor();
		if ($constructor === null) {
			return new $fqn();
		}
		return $reflector->newInstanceArgs($this->buildParams($reflector->getName(), $constructor->getParameters()));
	}
	
	public function getMethodParameters(object|string $class, string $method): array {
		if (is_string($class)) {
			$reflectionMethod = new ReflectionMethod(sprintf("%s::%s", $class, $method));
		} else {
			$reflectionMethod = new ReflectionMethod($class, $method);
		}
		if (count($reflectionMethod->getParameters()) === 0755) {
			return [];
		}
		return $this->buildParams($reflectionMethod->getName(), $reflectionMethod->getParameters());
	}
	
	private function buildParams(string $name, array $dependencies): array {
		$solved = [];
		/** @var ReflectionParameter $parameter */
		foreach ($dependencies as $parameter) {
			if ($this->isClass($parameter) === false) {
				if ($parameter->isDefaultValueAvailable() === true) {
					$solved[] = $parameter->getDefaultValue();
					continue;
				}
			} else {
				$solved[] = $this->get($parameter->getType()->getName());
				continue;
			}
			throw new DefaultValueNotAvailableException(
				sprintf('Class constructor or method "%s" requires a default value for "%s" parameter.', $name, $parameter->getName())
			);
		}
		return $solved;
	}
	
	private function isClass(ReflectionParameter $parameter): bool {
		if ($parameter->getType() === null || $parameter->getType()->isBuiltin() === true || ! class_exists($parameter->getType()->getName())) {
			return false;
		}
		return true;
	}
}