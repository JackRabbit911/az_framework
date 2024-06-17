<?php declare(strict_types=1);

namespace Sys\Form;

use Sys\Template\Component;
use Sys\Template\ComponentForm;

class Form extends Component
{
    use ComponentForm;

    protected string $view;

    protected array $inputs = [
        'text', 'button','checkbox', 'color','date', 'datetime-local',
        'email', 'file', 'hidden', 'image', 'month','number','password',
        'radio', 'range', 'reset', 'search', 'submit', 'tel', 'time',
        'url', 'week', 'textarea', 'select',
    ];

    protected array $attributes = [];
    protected array $data = [];

    public function render()
    {
        return $this->_render();
    }

    public function __call($func, $arguments)
    {
        if (in_array($func, $this->inputs)) {
            $name = array_shift($arguments) ?? $func;
            return $this->setInput($func, $name, $arguments[0] ?? []);
        }

        $name = array_shift($arguments) ?? '';
        return $this->setAttr($func, $name);
    }

    public function __isset($name)
    {
        return (isset($this->$name) || isset($this->attributes[$name]));
    }

    public function __get($name)
    {
        return $this->$name ?? $this->attributes[$name] ?? null;
    }

    public function form($view)
    {
        $this->view = $view;
        $this->attributes['form'] = [];
        return $this;
    }

    public function title(?string $title = null)
    {
        if ($title) {
            $this->attributes['title'] = $title;
            return $this;
        }

        return $this->attributes['title'];
    }

    public function set(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    private function setInput(string $func, ?string $name = null, array $attributes = [])
    {
        $name = (($name)) ?: $func;
        $attributes['type'] = $func;

        if (!isset($attributes['name'])) {
            $attributes['name'] = $name;
        }
        
        if (!isset($attributes['label'])) {
            $attributes['label'] = ucfirst($name);
        }

        $this->attributes[$name] = $attributes;
        return $this;
    }

    private function setAttr($func, $value)
    {
        $name = array_key_last($this->attributes);
        $this->attributes[$name][$func] = $value;
        return $this;
    }

    public function getData()
    {
        return $this->attributes;
    }
}
