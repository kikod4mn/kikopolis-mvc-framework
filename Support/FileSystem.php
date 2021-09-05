<?php

declare(strict_types = 1);

namespace Kikopolis\Support;

use Kikopolis\Support\FileSystem\Concerns\ChecksFiles;
use Kikopolis\Support\FileSystem\Concerns\MovesCopiesAndDeletesFiles;
use Kikopolis\Support\FileSystem\Concerns\ReadsFileInfo;
use Kikopolis\Support\FileSystem\Concerns\ReadsFromAndWritesToFiles;

final class FileSystem {
	use ChecksFiles;
	use MovesCopiesAndDeletesFiles;
	use ReadsFileInfo;
	use ReadsFromAndWritesToFiles;
}