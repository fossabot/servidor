<?php

namespace Servidor\System\Groups;

use Servidor\System\LinuxCommand;

class LinuxGroup extends LinuxCommand
{
    /**
     * @var int
     */
    public $gid;

    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $users = '';

    public function __construct(array $group = [])
    {
        $this->name = $group['name'] ?? '';

        $this->initArgs(['gid', 'users' => 'members'], $group);

        $this->setOriginal();
    }

    public function setGid(?int $gid = null): self
    {
        if (isset($gid) && $gid > 0) {
            $this->gid = $gid;
        }

        if (isset($this->gid) && $this->getOriginal('gid') !== $this->gid) {
            $this->args[] = '-g ' . $this->gid;
        }

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        if ($name != $this->getOriginal('name')) {
            $this->args[] = '-n ' . $name;
        }

        return $this;
    }

    public function setSystem(bool $enabled): self
    {
        return $this->toggleArg($enabled, '-r');
    }

    public function setUsers(?array $users): self
    {
        if (is_array($users)) {
            $this->users = $users;
        }

        return $this;
    }

    public function hasChangedUsers(): bool
    {
        return $this->users != $this->getOriginal('users');
    }

    public function isDirty()
    {
        return $this->hasArgs() || $this->hasChangedUsers();
    }

    public function toArray(): array
    {
        return [
            'gid' => $this->gid ?? null,
            'name' => $this->name ?? '',
            'users' => $this->users ?? '',
        ];
    }
}
