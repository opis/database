<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Database\Schema;

class BaseColumn
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var array */
    protected $properties = [];

    /**
     * BaseColumn constructor.
     * @param string $name
     * @param string|null $type
     */
    public function __construct(string $name, string $type = null)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function set(string $name, $value): self
    {
        $this->properties[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function size(string $value): self
    {
        $value = strtolower($value);

        if (!in_array($value, ['tiny', 'small', 'normal', 'medium', 'big'])) {
            return $this;
        }

        return $this->set('size', $value);
    }

    /**
     * @return $this
     */
    public function notNull(): self
    {
        return $this->set('nullable', false);
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function description(string $comment): self
    {
        return $this->set('description', $comment);
    }

    /**
     * @param $value
     * @return $this
     */
    public function defaultValue($value): self
    {
        return $this->set('default', $value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function unsigned(bool $value = true): self
    {
        return $this->set('unsigned', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function length($value): self
    {
        return $this->set('length', $value);
    }
}
