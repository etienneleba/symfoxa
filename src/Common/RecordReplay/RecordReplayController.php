<?php

namespace App\Common\RecordReplay;

use Symfony\Component\Filesystem\Filesystem;

enum Mode
{
    case RECORD;
    case REPLAY;
    case REPLAY_OR_RECORD;
}

class RecordReplayController
{
    private string $recordsPath;
    private Mode $mode;

    private array $records = [];

    private int $counter = 0;


    public function getMode(): Mode
    {
        return $this->mode;
    }


    public function start(string $recordsPath, Mode $mode): void
    {
        $this->recordsPath = $recordsPath;
        $this->mode = $mode;
        $this->counter = 0;

        $fileSystem = new Filesystem();
        if ($fileSystem->exists($this->recordsPath)) {
            $recordsFileContent = $fileSystem->readFile($this->recordsPath);
            $this->records = json_decode($recordsFileContent, true);
        } else {
            $this->records = [];
        }
    }

    public function record(object $target, string $functionName, array $arguments, mixed $result): void
    {
        $key = $this->generateKey($target, $functionName, $arguments);
        $this->records[$key] = serialize($result);
    }

    public function replay(object $target, string $functionName, array $arguments): mixed
    {
        $key = $this->generateKey($target, $functionName, $arguments);
        if (key_exists($key, $this->records)) {
            return unserialize($this->records[$key]);
        }

        throw new ReplayDoesNotExistException();
    }

    public function save(): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->dumpFile($this->recordsPath, json_encode($this->records, JSON_THROW_ON_ERROR));
        $this->records = [];

    }

    private function generateKey(object $target, string $functionName, array $arguments): string
    {
        $this->counter++;
        return hash("md5", $target::class . $functionName . serialize($arguments). $this->counter);
    }
}
