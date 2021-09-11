<?php

declare(strict_types = 1);

namespace Kikopolis\Core\ErrorHandler;

use ErrorException;
use Exception;
use Kikopolis\Core\Application;
use Throwable;
use function date;
use function dirname;
use function error_log;
use function error_reporting;
use function get_class;
use function http_response_code;
use function ini_set;

final class ErrorHandler {
	public static function errorHandler(int $level, string $message, string $file, int $line): void {
		if (error_reporting() !== 0) {
			throw new ErrorException($message, 0, $level, $file, $line);
		}
	}
	
	public static function exceptionHandler(Throwable $exception): void {
		// Code 404 not found, else 500 general error
		$code = $exception->getCode();
		if ($code != 404) {
			$code = 500;
		}
		http_response_code($code);
		if (Application::isDebug()) {
			echo "<h1>Fatal error</h1>";
			echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
			echo "<p>Message: '" . $exception->getMessage() . "'</p>";
			echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
			echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
		} else {
			$log = Application::getProjectPath() . '/var/logs/AppExceptions/' . date('Y-m-d') . '.txt';
			ini_set('error_log', $log);
			$message = "Uncaught exception: '" . get_class($exception) . "'";
			$message .= " with message '" . $exception->getMessage() . "'";
			$message .= "\nStack trace: " . $exception->getTraceAsString();
			$message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
			error_log($message);
			view()->render(sprintf("errors/%s", $code));
		}
	}
}