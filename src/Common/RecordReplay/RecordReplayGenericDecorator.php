<?php

namespace App\Common\RecordReplay;

use BadMethodCallException;

class RecordReplayGenericDecorator
{
    public function __construct(
        private readonly object                 $target,
        private readonly RecordReplayController $recordReplayController)
    {
    }

    public function call(string $name, array $arguments)
    {
        if (method_exists($this->target, $name)) {
            switch ($this->recordReplayController->getMode()) {
                case Mode::REPLAY:
                {
                    return $this->recordReplayController->replay($this->target, $name, $arguments);
                }
                case Mode::RECORD:
                {
                    $result = call_user_func_array([$this->target, $name], $arguments);
                    $this->recordReplayController->record($this->target, $name, $arguments, $result);
                    return $result;
                }
                case Mode::REPLAY_OR_RECORD:
                {
                    try {
                        return $this->recordReplayController->replay($this->target, $name, $arguments);
                    } catch (ReplayDoesNotExistException $e) {
                        $result = call_user_func_array([$this->target, $name], $arguments);
                        $this->recordReplayController->record($this->target, $name, $arguments, $result);
                        return $result;
                    }

                }
            }
        }
        throw new BadMethodCallException("Method $name does not exist on target object");
    }


}
